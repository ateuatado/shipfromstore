<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

/**
 * Rebuilds the pilot order scenario without touching employees, units or auth.
 *
 * Run with: php spark db:seed PilotRandomOrdersSeeder
 */
final class PilotRandomOrdersSeeder extends Seeder
{
    private const ORDER_COUNT_PER_UNIT = 5;
    private const SLA_SECONDS = 7200;

    public function run(): void
    {
        $db = $this->db;
        $config = config('Map');
        $units = $config->units;
        $employees = $db->table('employees')->get()->getResultArray();
        $managers = [];
        $couriers = [];

        foreach ($employees as $employee) {
            if ($employee['role'] === 'gestor_unidade') {
                $managers[$employee['unit_id']] = $employee;
            }
            if ($employee['role'] === 'entregador') {
                $couriers[$employee['unit_id']][] = $employee;
            }
        }

        foreach ($couriers as &$unitCouriers) {
            usort($unitCouriers, static fn (array $left, array $right): int => strcmp($left['matricula'], $right['matricula']));
        }
        unset($unitCouriers);

        $this->validatePilotStructure($units, $managers, $couriers);

        $db->transStart();
        $db->table('delivery_assignments')->emptyTable();
        $db->table('orders')->emptyTable();

        mt_srand(20261028);
        $addresses = $this->addressCatalog();
        $now = Time::now('UTC');
        $sequence = 1;
        $feasible = 0;

        foreach ($units as $unit) {
            $unitId = $unit['id'];
            $areaId = $unit['areaId'];
            $unitCouriers = $couriers[$unitId] ?? [];

            for ($slot = 0; $slot < self::ORDER_COUNT_PER_UNIT; $slot++) {
                [$pickup, $delivery] = $this->drawFeasiblePair($unit, $addresses);
                $status = ['pending_acceptance', 'assigned', 'collected', 'in_transit', 'delivered'][$slot];
                $receivedAt = $now->subMinutes(20 + $slot)->toDateTimeString();
                $acceptedAt = in_array($status, ['assigned', 'collected', 'in_transit', 'delivered'], true)
                    ? $now->subMinutes(12 + $slot)->toDateTimeString() : null;
                $collectedAt = in_array($status, ['collected', 'in_transit', 'delivered'], true)
                    ? $now->subMinutes(7 + $slot)->toDateTimeString() : null;
                $deliveredAt = $status === 'delivered' ? $now->subMinutes(2)->toDateTimeString() : null;
                $contract = 'CT-2026-R' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

                $db->table('orders')->insert([
                    'contract' => $contract,
                    'customer_name' => 'Contato Piloto ' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
                    'unit_id' => $unitId,
                    'area_id' => $areaId,
                    'pickup_address' => $pickup['address'],
                    'delivery_address' => $delivery['address'],
                    'pickup_lat' => $pickup['lat'],
                    'pickup_lng' => $pickup['lng'],
                    'delivery_lat' => $delivery['lat'],
                    'delivery_lng' => $delivery['lng'],
                    'status' => $status,
                    'received_at' => $receivedAt,
                    'accepted_at' => $acceptedAt,
                    'collected_at' => $collectedAt,
                    'delivered_at' => $deliveredAt,
                    'created_at' => $receivedAt,
                    'updated_at' => $now->toDateTimeString(),
                ]);

                if ($status !== 'pending_acceptance') {
                    $courier = $unitCouriers[$slot - 1];
                    $db->table('delivery_assignments')->insert([
                        'contract' => $contract,
                        'unit_id' => $unitId,
                        'courier_user_id' => $courier['user_id'],
                        'assigned_by_user_id' => $managers[$unitId]['user_id'],
                        'status' => $status,
                        'assigned_at' => $acceptedAt,
                        'collection_confirmed_at' => $collectedAt,
                        'delivered_at' => $deliveredAt,
                        'created_at' => $acceptedAt,
                        'updated_at' => $now->toDateTimeString(),
                    ]);
                }

                $feasible++;
                $sequence++;
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            throw new \RuntimeException('Não foi possível recriar a massa de pedidos do piloto.');
        }

        log_message('info', 'Massa aleatória do piloto recriada: {feasible} dentro do SLA.', ['feasible' => $feasible]);
        CLI::write(sprintf('Massa recriada: %d pedidos, %d unidades, %d dentro do SLA de 120 minutos.', $sequence - 1, count($units), $feasible), 'green');
    }

    private function drawFeasiblePair(array $unit, array $addresses): array
    {
        $attempts = 0;
        do {
            $pickup = $addresses[array_rand($addresses)];
            $delivery = $addresses[array_rand($addresses)];
            $attempts++;
            $route = $this->fallbackRoute([
                [$unit['lng'], $unit['lat']],
                [$pickup['lng'], $pickup['lat']],
                [$delivery['lng'], $delivery['lat']],
            ]);
        } while ($pickup['address'] === $delivery['address'] || $route['plannedSeconds'] > self::SLA_SECONDS);

        if ($attempts > 1000) {
            throw new \RuntimeException('Não foi possível encontrar uma rota válida dentro do SLA.');
        }

        return [$pickup, $delivery];
    }

    private function fallbackRoute(array $coordinates): array
    {
        $meters = 0.0;
        for ($index = 1; $index < count($coordinates); $index++) {
            $meters += $this->haversine($coordinates[$index - 1][1], $coordinates[$index - 1][0], $coordinates[$index][1], $coordinates[$index][0]);
        }

        $travelSeconds = (int) round(($meters * 1.2 / 25000) * 3600);
        $plannedSeconds = $travelSeconds + 600 + 300 + 900;

        return ['plannedSeconds' => $plannedSeconds, 'travelSeconds' => $travelSeconds];
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $radius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $value = sin($latDelta / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return 2 * $radius * asin(min(1, sqrt($value)));
    }

    private function validatePilotStructure(array $units, array $managers, array $couriers): void
    {
        foreach ($units as $unit) {
            if (! isset($managers[$unit['id']]) || count($couriers[$unit['id']] ?? []) < self::ORDER_COUNT_PER_UNIT - 1) {
                throw new \RuntimeException('Usuários do piloto incompletos para ' . $unit['id'] . '. Execute PilotUsersSeeder antes.');
            }
        }
    }

    /** Curated real street addresses within the pilot territory, with map coordinates. */
    private function addressCatalog(): array
    {
        return [
            ['address' => 'Rua Mergenthaler, 595 — Vila Leopoldina, São Paulo/SP', 'lat' => -23.5294829, 'lng' => -46.7386242],
            ['address' => 'Avenida Queiroz Filho, 1700 — Vila Hamburguesa, São Paulo/SP', 'lat' => -23.5455, 'lng' => -46.7220],
            ['address' => 'Avenida Imperatriz Leopoldina, 845 — Vila Leopoldina, São Paulo/SP', 'lat' => -23.5314, 'lng' => -46.7274],
            ['address' => 'Rua Coriolano, 120 — Vila Romana, São Paulo/SP', 'lat' => -23.5288, 'lng' => -46.6970],
            ['address' => 'Rua Cerro Corá, 245 — Alto da Lapa, São Paulo/SP', 'lat' => -23.5352, 'lng' => -46.7048],
            ['address' => 'Avenida Sumaré, 1450 — Perdizes, São Paulo/SP', 'lat' => -23.5467, 'lng' => -46.6817],
            ['address' => 'Rua Clélia, 550 — Lapa, São Paulo/SP', 'lat' => -23.5241, 'lng' => -46.6995],
            ['address' => 'Rua Tito, 510 — Vila Romana, São Paulo/SP', 'lat' => -23.5237, 'lng' => -46.6904],
            ['address' => 'Avenida Francisco Matarazzo, 1700 — Água Branca, São Paulo/SP', 'lat' => -23.5276, 'lng' => -46.6775],
            ['address' => 'Rua Harmonia, 797 — Vila Madalena, São Paulo/SP', 'lat' => -23.5516, 'lng' => -46.6915],
            ['address' => 'Rua Teodoro Sampaio, 1020 — Pinheiros, São Paulo/SP', 'lat' => -23.5557, 'lng' => -46.6860],
            ['address' => 'Rua dos Pinheiros, 870 — Pinheiros, São Paulo/SP', 'lat' => -23.5678, 'lng' => -46.6887],
            ['address' => 'Avenida Brigadeiro Faria Lima, 2232 — Pinheiros, São Paulo/SP', 'lat' => -23.5727, 'lng' => -46.6907],
            ['address' => 'Avenida Rebouças, 3970 — Pinheiros, São Paulo/SP', 'lat' => -23.5745, 'lng' => -46.6918],
            ['address' => 'Rua Oscar Freire, 800 — Jardins, São Paulo/SP', 'lat' => -23.5652, 'lng' => -46.6738],
            ['address' => 'Avenida Jabaquara, 360 — Saúde, São Paulo/SP', 'lat' => -23.6106, 'lng' => -46.6420],
            ['address' => 'Avenida Ibirapuera, 2120 — Moema, São Paulo/SP', 'lat' => -23.6015, 'lng' => -46.6653],
            ['address' => 'Avenida Morumbi, 5387 — Morumbi, São Paulo/SP', 'lat' => -23.6112, 'lng' => -46.7048],
            ['address' => 'Avenida Giovanni Gronchi, 5819 — Vila Andrade, São Paulo/SP', 'lat' => -23.6358, 'lng' => -46.7366],
            ['address' => 'Rua Butantã, 298 — Pinheiros, São Paulo/SP', 'lat' => -23.5674, 'lng' => -46.6932],
            ['address' => 'Avenida Nossa Senhora da Assunção, 647 — Butantã, São Paulo/SP', 'lat' => -23.5690, 'lng' => -46.7268],
            ['address' => 'Avenida Escola Politécnica, 1000 — Butantã, São Paulo/SP', 'lat' => -23.5612, 'lng' => -46.7334],
            ['address' => 'Avenida Corifeu de Azevedo Marques, 3300 — Butantã, São Paulo/SP', 'lat' => -23.5662, 'lng' => -46.7489],
            ['address' => 'Avenida dos Autonomistas, 4900 — Osasco/SP', 'lat' => -23.5378, 'lng' => -46.7797],
            ['address' => 'Avenida Mutinga, 2500 — Pirituba, São Paulo/SP', 'lat' => -23.4926, 'lng' => -46.7273],
            ['address' => 'Avenida Raimundo Pereira de Magalhães, 12000 — Pirituba, São Paulo/SP', 'lat' => -23.4770, 'lng' => -46.7377],
            ['address' => 'Rua Barão de Jaceguai, 1200 — Campo Belo, São Paulo/SP', 'lat' => -23.6251, 'lng' => -46.6721],
            ['address' => 'Avenida Santo Amaro, 3330 — Brooklin, São Paulo/SP', 'lat' => -23.6151, 'lng' => -46.6815],
            ['address' => 'Rua Joaquim Nabuco, 1500 — Brooklin, São Paulo/SP', 'lat' => -23.6183, 'lng' => -46.6875],
            ['address' => 'Avenida Professor Alfonso Bovero, 1200 — Sumaré, São Paulo/SP', 'lat' => -23.5447, 'lng' => -46.6840],
        ];
    }
}
