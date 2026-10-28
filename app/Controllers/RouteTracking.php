<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

final class RouteTracking extends BaseController
{
    public function index(): string|ResponseInterface
    {
        $employee = $this->managerEmployee();
        if ($employee === null) {
            return $this->response->setStatusCode(403)->setBody('Acesso não permitido.');
        }

        return view('pages/routes', [
            'title' => 'Percursos dos entregadores | ShipFromStore',
            'employee' => $employee,
        ]);
    }

    public function data(): ResponseInterface
    {
        $employee = $this->managerEmployee();
        if ($employee === null) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não permitido.']);
        }

        $db = db_connect();
        $builder = $db->table('delivery_assignments da')
            ->select('da.id AS assignmentId, da.contract, da.unit_id AS unitId, da.status, da.assigned_at AS assignedAt, da.collection_confirmed_at AS collectedAt, da.delivered_at AS deliveredAt, e.name AS courierName, o.pickup_address AS pickupAddress, o.delivery_address AS deliveryAddress')
            ->join('employees e', 'e.user_id = da.courier_user_id')
            ->join('orders o', 'o.contract = da.contract')
            ->whereIn('da.status', ['collected', 'in_transit', 'delivered']);
        $this->applyScope($builder, $employee);
        $assignments = $builder->orderBy('da.updated_at', 'DESC')->get()->getResultArray();

        if ($assignments === []) {
            return $this->response->setJSON(['routes' => []]);
        }

        $ids = array_column($assignments, 'assignmentId');
        $pointRows = $db->table('delivery_tracking_points')
            ->select('assignment_id AS assignmentId, latitude, longitude, accuracy_meters AS accuracy, sequence, recorded_at AS recordedAt')
            ->whereIn('assignment_id', $ids)
            ->orderBy('assignment_id', 'ASC')
            ->orderBy('sequence', 'ASC')
            ->get()->getResultArray();
        $points = [];
        foreach ($pointRows as $point) {
            $points[$point['assignmentId']][] = [
                'latitude' => (float) $point['latitude'],
                'longitude' => (float) $point['longitude'],
                'accuracy' => $point['accuracy'] === null ? null : (float) $point['accuracy'],
                'sequence' => (int) $point['sequence'],
                'recordedAt' => $point['recordedAt'],
            ];
        }

        foreach ($assignments as &$assignment) {
            $assignment['points'] = $points[$assignment['assignmentId']] ?? [];
        }
        unset($assignment);

        return $this->response->setJSON(['routes' => $assignments]);
    }

    private function managerEmployee(): ?array
    {
        $user = auth()->user();
        $employee = db_connect()->table('employees')->where('user_id', $user->id)->get()->getRowArray();

        return $employee !== null && in_array($employee['role'], ['gestor_unidade', 'gestor_area', 'gerente'], true) ? $employee : null;
    }

    private function applyScope($builder, array $employee): void
    {
        if ($employee['role'] === 'gestor_unidade') {
            $builder->where('da.unit_id', $employee['unit_id']);
        } elseif ($employee['role'] === 'gestor_area') {
            $builder->where('o.area_id', $employee['area_id']);
        }
    }
}
