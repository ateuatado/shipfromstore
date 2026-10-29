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
        $unitNames = implode(', ', array_column($result['units'], 'id'));
        return redirect()->to(site_url('simulator'))->with('message', sprintf('Uma coleta (%s) foi disparada para as unidades %s. A primeira unidade que aceitar vence; prazo: 15 minutos.', $result['collectionId'], $unitNames));
    }

    public function randomLocation(): ResponseInterface
    {
        if ($this->adminEmployee() === null) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não permitido.']);
        }

        $locations = [
            ['address' => 'Rua Mergenthaler, 595 — Vila Leopoldina, São Paulo/SP', 'lat' => -23.5294829, 'lng' => -46.7386242],
            ['address' => 'Avenida Queiroz Filho, 1700 — Vila Hamburguesa, São Paulo/SP', 'lat' => -23.5455, 'lng' => -46.7220],
            ['address' => 'Rua Coriolano, 120 — Vila Romana, São Paulo/SP', 'lat' => -23.5288, 'lng' => -46.6970],
            ['address' => 'Rua Cerro Corá, 245 — Alto da Lapa, São Paulo/SP', 'lat' => -23.5352, 'lng' => -46.7048],
            ['address' => 'Avenida Sumaré, 1450 — Perdizes, São Paulo/SP', 'lat' => -23.5467, 'lng' => -46.6817],
            ['address' => 'Rua Tito, 510 — Vila Romana, São Paulo/SP', 'lat' => -23.5237, 'lng' => -46.6904],
            ['address' => 'Avenida Francisco Matarazzo, 1700 — Água Branca, São Paulo/SP', 'lat' => -23.5276, 'lng' => -46.6775],
            ['address' => 'Rua Harmonia, 797 — Vila Madalena, São Paulo/SP', 'lat' => -23.5516, 'lng' => -46.6915],
            ['address' => 'Avenida Jabaquara, 360 — Saúde, São Paulo/SP', 'lat' => -23.6106, 'lng' => -46.6420],
            ['address' => 'Avenida Ibirapuera, 2120 — Moema, São Paulo/SP', 'lat' => -23.6015, 'lng' => -46.6653],
            ['address' => 'Avenida Mutinga, 2500 — Pirituba, São Paulo/SP', 'lat' => -23.4926, 'lng' => -46.7273],
            ['address' => 'Avenida dos Autonomistas, 4900 — Osasco/SP', 'lat' => -23.5378, 'lng' => -46.7797],
        ];

        return $this->response->setJSON($locations[array_rand($locations)]);
    }

    private function adminEmployee(): ?array
    {
        $user = auth()->user();
        $employee = db_connect()->table('employees')->where('user_id', $user->id)->get()->getRowArray();
        return ($employee !== null && $employee['role'] === 'administrador') ? $employee : null;
    }
}
