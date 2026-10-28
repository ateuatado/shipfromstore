<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('pages/home', ['title' => 'ShipFromStore']);
    }

    public function dashboard(): string
    {
        return $this->operations('dashboard', 'Pedidos');
    }

    public function pickups(): string
    {
        return $this->operations('pickups', 'Coletas');
    }

    public function deliveries(): string
    {
        return $this->operations('deliveries', 'Pedidos entregues');
    }

    private function operations(string $page, string $title): string
    {
        $user = auth()->user();
        $employee = db_connect()->table('employees')
            ->where('user_id', $user->id)
            ->get()->getRowArray();
        $orders = $this->ordersForEmployee($employee, (int) $user->id);

        if (($employee['role'] ?? null) === 'entregador') {
            return view('pages/courier', ['title' => 'Minhas coletas | ShipFromStore', 'employee' => $employee, 'orders' => $orders]);
        }

        if (($employee['role'] ?? null) === 'gestor_unidade') {
            $orders = array_values(array_filter($orders, static fn (array $order): bool => $order['unitId'] === $employee['unit_id']));
        } elseif (($employee['role'] ?? null) === 'gestor_area') {
            $orders = array_values(array_filter($orders, static fn (array $order): bool => $order['areaId'] === $employee['area_id']));
        }

        return view('pages/' . $page, ['title' => $title . ' | ShipFromStore', 'employee' => $employee, 'orders' => $orders]);
    }

    private function ordersForEmployee(?array $employee, int $userId): array
    {
        $builder = db_connect()->table('orders o')
            ->select('o.contract, o.customer_name AS customerName, o.unit_id AS unitId, o.area_id AS areaId, o.pickup_address AS pickupAddress, o.delivery_address AS deliveryAddress, o.pickup_lat AS pickupLat, o.pickup_lng AS pickupLng, o.delivery_lat AS deliveryLat, o.delivery_lng AS deliveryLng, o.status, o.received_at AS receivedAt, o.accepted_at AS acceptedAt, o.collected_at AS collectedAt, o.delivered_at AS deliveredAt');

        if (($employee['role'] ?? null) === 'entregador') {
            $builder->join('delivery_assignments da', 'da.contract = o.contract')
                ->where('da.courier_user_id', $userId);
        }

        return $builder->orderBy("FIELD(o.status, 'pending_acceptance', 'assigned', 'collected', 'in_transit', 'delivered')", '', false)
            ->orderBy('o.received_at', 'DESC')
            ->get()->getResultArray();
    }
}
