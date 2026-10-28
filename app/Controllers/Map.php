<?php

namespace App\Controllers;

use App\Services\Maps\RouteEstimator;
use CodeIgniter\HTTP\ResponseInterface;

class Map extends BaseController
{
    public function index(): string
    {
        $config = config('Map');

        return view('pages/map', [
            'title' => 'Mapa de viabilidade | ShipFromStore',
            'units' => $config->units,
            'center' => $config->center,
            'radiusMeters' => $config->radiusMeters,
            'pickup' => $config->samplePickup,
            'delivery' => $config->sampleDelivery,
        ]);
    }

    public function route(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? [];
        $coordinates = $payload['coordinates'] ?? [];

        if (! is_array($coordinates) || count($coordinates) < 2) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Informe pelo menos dois pontos.']);
        }

        $config = config('Map');
        $result = (new RouteEstimator())->estimate($coordinates);
        $result['slaSeconds'] = $config->slaSeconds;
        $result['withinSla'] = $result['durationSeconds'] <= $result['slaSeconds'];

        return $this->response->setJSON($result);
    }
}
