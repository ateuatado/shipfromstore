<?php

declare(strict_types=1);

namespace App\Services;

final class TrackingPrivacy
{
    public function visiblePoints(array $points, float $pickupLat, float $pickupLng, string $mode = 'masked_500m'): array
    {
        if ($mode === 'exact') {
            return $points;
        }

        $radius = (int) config('TrackingPrivacy')->pickupMaskRadiusMeters;
        return array_values(array_filter($points, static function (array $point) use ($pickupLat, $pickupLng, $radius): bool {
            return self::distanceMeters($pickupLat, $pickupLng, (float) $point['latitude'], (float) $point['longitude']) >= $radius;
        }));
    }

    public function approximatePoints(array $points): array
    {
        $precision = (int) config('TrackingPrivacy')->publicLocationPrecision;
        return array_map(static function (array $point) use ($precision): array {
            $point['latitude'] = round((float) $point['latitude'], $precision);
            $point['longitude'] = round((float) $point['longitude'], $precision);
            return $point;
        }, $points);
    }

    public static function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $value = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($value)));
    }
}
