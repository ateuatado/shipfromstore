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
        $employee = db_connect()->table('employees')
            ->where('user_id', auth()->user()->id)
            ->get()->getRowArray();
        $orders = config('Orders')->sample;

        if (($employee['role'] ?? null) === 'gestor_unidade') {
            $orders = array_values(array_filter($orders, static fn (array $order): bool => $order['unitId'] === $employee['unit_id']));
        } elseif (($employee['role'] ?? null) === 'gestor_area') {
            $orders = array_values(array_filter($orders, static fn (array $order): bool => $order['areaId'] === $employee['area_id']));
        }

        return view('pages/' . $page, ['title' => $title . ' | ShipFromStore', 'employee' => $employee, 'orders' => $orders]);
    }
}
