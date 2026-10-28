<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

final class Assignments extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $context = $this->managerContext();
        if ($context === null) {
            return $this->response->setStatusCode(403)->setBody('Acesso não permitido.');
        }

        $db = db_connect();
        $orders = $db->table('orders')
            ->where('unit_id', $context['employee']['unit_id'])
            ->where('status', 'pending_acceptance')
            ->orderBy('received_at', 'ASC')
            ->get()->getResultArray();
        $couriers = $db->table('employees')
            ->where('role', 'entregador')
            ->where('unit_id', $context['employee']['unit_id'])
            ->orderBy('matricula', 'ASC')
            ->get()->getResultArray();

        return view('pages/assignments', [
            'title' => 'Atribuir coletas | ShipFromStore',
            'employee' => $context['employee'],
            'orders' => $orders,
            'couriers' => $couriers,
        ]);
    }

    public function assign(): ResponseInterface
    {
        $context = $this->managerContext();
        if ($context === null) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não permitido.']);
        }

        $contract = (string) $this->request->getPost('contract');
        $courierUserId = (int) $this->request->getPost('courier_user_id');
        $db = db_connect();
        $order = $db->table('orders')->where('contract', $contract)->where('unit_id', $context['employee']['unit_id'])->where('status', 'pending_acceptance')->get()->getRowArray();
        $courier = $db->table('employees')->where('user_id', $courierUserId)->where('role', 'entregador')->where('unit_id', $context['employee']['unit_id'])->get()->getRowArray();

        if ($order === null || $courier === null) {
            return redirect()->back()->with('error', 'Pedido ou entregador inválido para esta unidade.');
        }

        $now = Time::now('UTC')->toDateTimeString();
        $db->transStart();
        $db->table('orders')->where('id', $order['id'])->update(['status' => 'assigned', 'accepted_at' => $now, 'updated_at' => $now]);
        $db->table('delivery_assignments')->insert([
            'contract' => $order['contract'],
            'unit_id' => $order['unit_id'],
            'courier_user_id' => $courierUserId,
            'assigned_by_user_id' => auth()->user()->id,
            'status' => 'assigned',
            'assigned_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $db->transComplete();

        return redirect()->to(site_url('dashboard/assignments'))->with('message', 'Coleta aceita e atribuída ao entregador.');
    }

    private function managerContext(): ?array
    {
        $user = auth()->user();
        $employee = db_connect()->table('employees')->where('user_id', $user->id)->get()->getRowArray();
        return ($employee !== null && $employee['role'] === 'gestor_unidade') ? ['employee' => $employee] : null;
    }
}
