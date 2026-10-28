<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

final class PilotOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $db = $this->db;
        $orders = $db->table('orders');
        $assignments = $db->table('delivery_assignments');
        $employees = $db->table('employees')->get()->getResultArray();
        $managers = [];
        $couriers = [];
        $mapUnits = [];
        foreach (config('Map')->units as $mapUnit) {
            $mapUnits[$mapUnit['id']] = $mapUnit;
        }

        foreach ($employees as $employee) {
            if ($employee['role'] === 'gestor_unidade') {
                $managers[$employee['unit_id']] = $employee;
            }
            if ($employee['role'] === 'entregador') {
                $couriers[$employee['unit_id']][] = $employee;
            }
        }

        $sequence = 1;
        foreach ($couriers as $unitId => $unitCouriers) {
            usort($unitCouriers, static fn (array $left, array $right): int => strcmp($left['matricula'], $right['matricula']));
            $areaId = $unitCouriers[0]['area_id'];
            $unitPosition = $mapUnits[$unitId];

            $pendingContract = 'CT-2026-P' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $this->insertOrder($orders, [
                'contract' => $pendingContract,
                'customer_name' => 'Cliente Demonstração P' . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT),
                'unit_id' => $unitId,
                'area_id' => $areaId,
                'pickup_address' => 'Rua Demonstrativa, ' . (100 + $sequence) . ' — Área Sintética',
                'delivery_address' => 'Avenida de Destino, ' . (200 + $sequence) . ' — Área Sintética',
                'pickup_lat' => $unitPosition['lat'] + 0.005,
                'pickup_lng' => $unitPosition['lng'] + 0.005,
                'delivery_lat' => $unitPosition['lat'] - 0.008,
                'delivery_lng' => $unitPosition['lng'] - 0.008,
                'status' => 'pending_acceptance',
                'received_at' => '2026-10-28 12:00:00',
            ]);

            foreach ($unitCouriers as $slot => $courier) {
                $contract = 'CT-2026-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                $status = ['assigned', 'assigned', 'collected', 'in_transit', 'delivered'][$slot];
                $acceptedAt = '2026-10-28 11:30:00';
                $collectedAt = in_array($status, ['collected', 'in_transit', 'delivered'], true) ? '2026-10-28 11:45:00' : null;
                $deliveredAt = $status === 'delivered' ? '2026-10-28 12:40:00' : null;

                $this->insertOrder($orders, [
                    'contract' => $contract,
                    'customer_name' => 'Cliente Demonstração ' . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT),
                    'unit_id' => $unitId,
                    'area_id' => $areaId,
                    'pickup_address' => 'Rua de Coleta, ' . (300 + $sequence) . ' — Área Sintética',
                    'delivery_address' => 'Avenida de Entrega, ' . (400 + $sequence) . ' — Área Sintética',
                    'pickup_lat' => $unitPosition['lat'] + 0.004,
                    'pickup_lng' => $unitPosition['lng'] + 0.004,
                    'delivery_lat' => $unitPosition['lat'] - 0.007,
                    'delivery_lng' => $unitPosition['lng'] - 0.007,
                    'status' => $status,
                    'received_at' => '2026-10-28 11:00:00',
                    'accepted_at' => $acceptedAt,
                    'collected_at' => $collectedAt,
                    'delivered_at' => $deliveredAt,
                ]);

                if ($assignments->where('contract', $contract)->countAllResults() === 0) {
                    $assignments->insert([
                        'contract' => $contract,
                        'unit_id' => $unitId,
                        'courier_user_id' => $courier['user_id'],
                        'assigned_by_user_id' => $managers[$unitId]['user_id'],
                        'status' => $status,
                        'assigned_at' => '2026-10-28 11:30:00',
                        'collection_confirmed_at' => $collectedAt,
                        'delivered_at' => $deliveredAt,
                    ]);
                }
                $sequence++;
            }
        }
    }

    private function insertOrder($orders, array $data): void
    {
        if ($orders->where('contract', $data['contract'])->countAllResults() === 0) {
            $orders->insert($data);
        } else {
            $orders->where('contract', $data['contract'])->update($data);
        }
    }
}
