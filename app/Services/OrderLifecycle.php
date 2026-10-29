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
            ->where('status', 'pending_acceptance')
            ->where('acceptance_deadline_at <=', $now)
            ->get()->getResultArray();

        if ($orders === []) {
            return 0;
        }

        $db->transStart();
        foreach ($orders as $order) {
            $db->table('orders')->where('id', $order['id'])->update([
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
        $unit = $this->nearestUnit($pickupLat, $pickupLng);
        $now = Time::now('UTC');
        $acceptanceDeadline = $now->addMinutes(config('Operational')->acceptanceWindowMinutes);
        $contract = 'SIM-' . $now->format('YmdHis') . '-' . random_int(10, 99);
        $db = db_connect();

        $db->table('orders')->insert([
            'contract' => $contract,
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

        return ['contract' => $contract, 'unit' => $unit, 'receivedAt' => $now->toDateTimeString(), 'acceptanceDeadlineAt' => $acceptanceDeadline->toDateTimeString()];
    }

    public function nearestUnit(float $latitude, float $longitude): array
    {
        $units = config('Map')->units;
        usort($units, static function (array $left, array $right) use ($latitude, $longitude): int {
            $leftDistance = TrackingPrivacy::distanceMeters($latitude, $longitude, (float) $left['lat'], (float) $left['lng']);
            $rightDistance = TrackingPrivacy::distanceMeters($latitude, $longitude, (float) $right['lat'], (float) $right['lng']);
            return $leftDistance <=> $rightDistance;
        });

        $unit = $units[0];
        $unit['distanceMeters'] = (int) round(TrackingPrivacy::distanceMeters($latitude, $longitude, (float) $unit['lat'], (float) $unit['lng']));
        return $unit;
    }
}
