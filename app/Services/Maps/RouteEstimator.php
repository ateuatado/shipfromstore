<?php

namespace App\Services\Maps;

use CodeIgniter\HTTP\CURLRequest;

final class RouteEstimator
{
    public function __construct(private readonly ?CURLRequest $client = null)
    {
    }

    /**
     * Estimates a road route using an OSRM-compatible endpoint.
     * Coordinates are expected as [lng, lat] pairs.
     */
    public function estimate(array $coordinates): array
    {
        $fallback = $this->fallback($coordinates);
        $baseUrl = rtrim((string) env('maps.osrmBaseUrl', 'https://router.project-osrm.org'), '/');

        if (count($coordinates) < 2) {
            return $fallback;
        }

        try {
            $client = $this->client ?? service('curlrequest', ['timeout' => 4, 'connect_timeout' => 2]);
            $encoded = implode(';', array_map(static fn (array $point): string => $point[0] . ',' . $point[1], $coordinates));
            $response = $client->get($baseUrl . '/route/v1/driving/' . $encoded, [
                'query' => ['overview' => 'false', 'steps' => 'false'],
            ]);
            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

            if (($data['code'] ?? null) !== 'Ok' || empty($data['routes'][0])) {
                return $fallback;
            }

            return [
                'provider' => 'osrm',
                'durationSeconds' => (int) round($data['routes'][0]['duration']),
                'distanceMeters' => (int) round($data['routes'][0]['distance']),
                'feasible' => true,
            ];
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function fallback(array $coordinates): array
    {
        $meters = 0.0;
        for ($i = 1; $i < count($coordinates); $i++) {
            $meters += $this->haversine($coordinates[$i - 1][1], $coordinates[$i - 1][0], $coordinates[$i][1], $coordinates[$i][0]);
        }

        // Conservative prototype estimate: 25 km/h average plus 20% road detour.
        $roadMeters = $meters * 1.2;
        $durationSeconds = (int) round(($roadMeters / 25000) * 3600);

        return [
            'provider' => 'haversine-fallback',
            'durationSeconds' => $durationSeconds,
            'distanceMeters' => (int) round($roadMeters),
            'feasible' => true,
        ];
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }
}
