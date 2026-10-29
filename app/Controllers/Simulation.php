<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\OrderLifecycle;
use CodeIgniter\HTTP\ResponseInterface;

final class Simulation extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $employee = $this->adminEmployee();
        if ($employee === null) {
            return $this->response->setStatusCode(403)->setBody('Acesso não permitido.');
        }

        (new OrderLifecycle())->processAcceptanceTimeouts();
        $orders = db_connect()->table('orders')->orderBy('created_at', 'DESC')->limit(20)->get()->getResultArray();
        return view('pages/simulation', [
            'title' => 'Simulador operacional | ShipFromStore',
            'employee' => $employee,
            'orders' => $orders,
            'map' => config('Map'),
        ]);
    }

    public function create(): ResponseInterface
    {
        $employee = $this->adminEmployee();
        if ($employee === null) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não permitido.']);
        }

        $payload = $this->request->getPost();
        $required = ['pickup_address', 'pickup_lat', 'pickup_lng', 'delivery_address', 'delivery_lat', 'delivery_lng'];
        foreach ($required as $field) {
            if (! isset($payload[$field]) || $payload[$field] === '') {
                return redirect()->back()->with('error', 'Preencha todos os dados da coleta e da entrega.');
            }
        }

        $result = (new OrderLifecycle())->createSimulation($payload, (int) auth()->user()->id);
        return redirect()->to(site_url('simulator'))->with('message', sprintf('Pedido %s enviado para %s. Prazo de aceite: 15 minutos.', $result['contract'], $result['unit']['id']));
    }

    private function adminEmployee(): ?array
    {
        $user = auth()->user();
        $employee = db_connect()->table('employees')->where('user_id', $user->id)->get()->getRowArray();
        return ($employee !== null && $employee['role'] === 'administrador') ? $employee : null;
    }
}
