<?php

namespace App\Controllers;

use App\Services\OrderLifecycle;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(): string
    {
        return view('pages/home', ['title' => 'ShipFromStore']);
    }

    public function dashboard(): string|ResponseInterface
    {
        return $this->operations('dashboard', 'Pedidos');
    }

    public function pickups(): string|ResponseInterface
    {
        return $this->operations('pickups', 'Coletas');
    }

    public function deliveries(): string|ResponseInterface
    {
        return $this->operations('deliveries', 'Pedidos entregues');
    }

    private function operations(string $page, string $title): string|ResponseInterface
    {
        (new OrderLifecycle())->processAcceptanceTimeouts();
        $user = auth()->user();
        $employee = db_connect()->table('employees')
            ->where('user_id', $user->id)
            ->get()->getRowArray();

        if (($employee['role'] ?? null) === 'administrador') {
            return redirect()->to(site_url('simulator'));
        }

        if (($employee['role'] ?? null) === 'entregador') {
            return view('pages/courier', [
                'title' => 'Minhas coletas | ShipFromStore',
                'employee' => $employee,
                'orders' => $this->ordersForEmployee($employee, (int) $user->id, ['assigned', 'collected', 'in_transit']),
                'completedOrders' => $this->ordersForEmployee($employee, (int) $user->id, ['delivered']),
            ]);
        }

        $orders = $this->ordersForEmployee($employee, (int) $user->id);

        if (($employee['role'] ?? null) === 'gestor_unidade') {
            $orders = array_values(array_filter($orders, static fn (array $order): bool => $order['unitId'] === $employee['unit_id']));
        } elseif (($employee['role'] ?? null) === 'gestor_area') {
            $orders = array_values(array_filter($orders, static fn (array $order): bool => $order['areaId'] === $employee['area_id']));
        }

        // Area and territory views represent one collection, not each unit
        // offer sent in parallel. Keep the first active offer per collection.
        if (($employee['role'] ?? null) !== 'gestor_unidade') {
            $orders = $this->uniqueCollections($orders);
        }

        return view('pages/' . $page, [
            'title' => $title . ' | ShipFromStore',
            'employee' => $employee,
            'orders' => $orders,
            'metrics' => $this->metrics($orders),
        ]);
    }

    private function metrics(array $orders): array
    {
        $deliveredDurations = [];
        foreach ($orders as $order) {
            if ($order['collectedAt'] !== null && $order['deliveredAt'] !== null) {
                $deliveredDurations[] = max(0, strtotime($order['deliveredAt']) - strtotime($order['collectedAt']));
            }
        }

        $onTime = count(array_filter($deliveredDurations, static fn (int $seconds): bool => $seconds <= 7200));

        return [
            'total' => count($orders),
            'pending' => count(array_filter($orders, static fn (array $order): bool => $order['status'] === 'pending_acceptance')),
            'active' => count(array_filter($orders, static fn (array $order): bool => in_array($order['status'], ['assigned', 'collected', 'in_transit'], true))),
            'delivered' => count(array_filter($orders, static fn (array $order): bool => $order['status'] === 'delivered')),
            'onTime' => $onTime,
            'slaRate' => $deliveredDurations === [] ? null : (int) round(($onTime / count($deliveredDurations)) * 100),
            'averageMinutes' => $deliveredDurations === [] ? null : (int) round(array_sum($deliveredDurations) / count($deliveredDurations) / 60),
        ];
    }

    private function ordersForEmployee(?array $employee, int $userId, ?array $statuses = null): array
    {
        $builder = db_connect()->table('orders o')
            ->select('o.contract, o.collection_id AS collectionId, o.customer_name AS customerName, o.unit_id AS unitId, o.area_id AS areaId, o.pickup_address AS pickupAddress, o.delivery_address AS deliveryAddress, o.pickup_lat AS pickupLat, o.pickup_lng AS pickupLng, o.delivery_lat AS deliveryLat, o.delivery_lng AS deliveryLng, o.status, o.received_at AS receivedAt, o.accepted_at AS acceptedAt, o.collected_at AS collectedAt, o.delivered_at AS deliveredAt, o.acceptance_deadline_at AS acceptanceDeadlineAt, o.sla_deadline_at AS slaDeadlineAt, o.partner_forwarded_at AS partnerForwardedAt, o.partner_name AS partnerName, da.id AS assignmentId, da.assigned_at AS assignedAt, da.collection_confirmed_at AS collectionConfirmedAt, da.courier_user_id AS courierUserId, courier.name AS courierName')
            ->where('o.status !=', 'offer_cancelled');

        if (($employee['role'] ?? null) === 'entregador') {
            $builder->join('delivery_assignments da', 'da.contract = o.contract')
                ->where('da.courier_user_id', $userId)
                ->where('da.unit_id', $employee['unit_id']);
        } else {
            $builder->join('delivery_assignments da', 'da.contract = o.contract', 'left');
        }
        $builder->join('employees courier', 'courier.user_id = da.courier_user_id', 'left');

        if ($statuses !== null) {
            $builder->whereIn('o.status', $statuses);
        }

        $statusOrder = ($employee['role'] ?? null) === 'entregador'
            ? "FIELD(o.status, 'in_transit', 'collected', 'assigned', 'delivered', 'pending_acceptance')"
            : "FIELD(o.status, 'pending_acceptance', 'assigned', 'collected', 'in_transit', 'delivered')";

        return $builder->orderBy($statusOrder, '', false)
            ->orderBy('o.received_at', 'DESC')
            ->get()->getResultArray();
    }

    private function uniqueCollections(array $orders): array
    {
        $unique = [];
        foreach ($orders as $order) {
            $key = (string) ($order['collectionId'] ?? $order['contract']);
            if (! isset($unique[$key])) {
                $unique[$key] = $order;
            }
        }

        return array_values($unique);
    }
}
