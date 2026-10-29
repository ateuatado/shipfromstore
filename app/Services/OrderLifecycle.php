<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\I18n\Time;

final class OrderLifecycle
{
    public function processAcceptanceTimeouts(): int
    {
        $now = Time::now('UTC')->toDateTimeString();
        $db = db_connect();
        $orders = $db->table('orders')
            ->select('collection_id')
            ->where('status', 'pending_acceptance')
            ->where('acceptance_deadline_at <=', $now)
            ->groupBy('collection_id')
            ->get()->getResultArray();

        if ($orders === []) {
            return 0;
        }

        $db->transStart();
        foreach ($orders as $order) {
            $db->table('orders')
                ->where('collection_id', $order['collection_id'])
                ->where('status', 'pending_acceptance')
                ->update([
                'status' => 'partner_forwarded',
                'partner_forwarded_at' => $now,
                'partner_name' => config('Operational')->partnerName,
                'updated_at' => $now,
            ]);
        }
        $db->transComplete();

        return count($orders);
    }

    public function createSimulation(array $payload, int $createdByUserId): array
    {
        $pickupLat = (float) $payload['pickup_lat'];
        $pickupLng = (float) $payload['pickup_lng'];
        $deliveryLat = (float) $payload['delivery_lat'];
        $deliveryLng = (float) $payload['delivery_lng'];
        $units = $this->nearestUnits($pickupLat, $pickupLng, (int) config('Operational')->offerUnitCount);
        $now = Time::now('UTC');
        $acceptanceDeadline = $now->addMinutes(config('Operational')->acceptanceWindowMinutes);
        $collectionId = 'SIM-' . $now->format('YmdHis') . '-' . random_int(10, 99);
        $db = db_connect();

        foreach ($units as $index => $unit) {
            $db->table('orders')->insert([
                'contract' => $collectionId . '-U' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'collection_id' => $collectionId,
                'customer_name' => (string) ($payload['customer_name'] ?? 'Cliente simulado'),
                'unit_id' => $unit['id'],
                'area_id' => $unit['areaId'],
                'pickup_address' => (string) $payload['pickup_address'],
                'delivery_address' => (string) $payload['delivery_address'],
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'delivery_lat' => $deliveryLat,
                'delivery_lng' => $deliveryLng,
                'status' => 'pending_acceptance',
                'received_at' => $now->toDateTimeString(),
                'acceptance_deadline_at' => $acceptanceDeadline->toDateTimeString(),
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ]);
        }

        return ['collectionId' => $collectionId, 'units' => $units, 'receivedAt' => $now->toDateTimeString(), 'acceptanceDeadlineAt' => $acceptanceDeadline->toDateTimeString()];
    }

    public function nearestUnit(float $latitude, float $longitude): array
    {
        return $this->nearestUnits($latitude, $longitude, 1)[0];
    }

    public function nearestUnits(float $latitude, float $longitude, int $limit): array
    {
        $units = config('Map')->units;
        usort($units, static function (array $left, array $right) use ($latitude, $longitude): int {
            $leftDistance = TrackingPrivacy::distanceMeters($latitude, $longitude, (float) $left['lat'], (float) $left['lng']);
            $rightDistance = TrackingPrivacy::distanceMeters($latitude, $longitude, (float) $right['lat'], (float) $right['lng']);
            return $leftDistance <=> $rightDistance;
        });

        $units = array_slice($units, 0, max(1, $limit));
        foreach ($units as &$unit) {
            $unit['distanceMeters'] = (int) round(TrackingPrivacy::distanceMeters($latitude, $longitude, (float) $unit['lat'], (float) $unit['lng']));
        }
        unset($unit);
        return $units;
    }
}
