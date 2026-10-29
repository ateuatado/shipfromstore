<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use App\Services\TrackingPrivacy;

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
            ->select('da.id AS assignmentId, da.contract, da.unit_id AS unitId, da.status, da.assigned_at AS assignedAt, da.collection_confirmed_at AS collectedAt, da.delivered_at AS deliveredAt, e.name AS courierName, o.pickup_lat AS pickupLat, o.pickup_lng AS pickupLng, o.pickup_privacy_mode AS pickupPrivacyMode')
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

        $privacy = new TrackingPrivacy();
        foreach ($assignments as &$assignment) {
            $rawPoints = $points[$assignment['assignmentId']] ?? [];
            $visiblePoints = $privacy->visiblePoints(
                $rawPoints,
                (float) $assignment['pickupLat'],
                (float) $assignment['pickupLng'],
                (string) ($assignment['pickupPrivacyMode'] ?: 'masked_500m'),
            );
            $assignment['points'] = $visiblePoints;
            $assignment['privacyNotice'] = 'Percurso exibido a partir de 500 m do ponto de coleta.';
            $assignment['rawPointCount'] = count($rawPoints);
            $assignment['distanceMeters'] = $this->routeDistance($visiblePoints);
            $assignment['minutesAfterCollection'] = $this->elapsedMinutes($assignment['collectedAt'], $assignment['deliveredAt']);
            unset($assignment['pickupLat'], $assignment['pickupLng'], $assignment['pickupPrivacyMode']);
        }
        unset($assignment);

        return $this->response->setJSON(['routes' => $assignments]);
    }

    public function share(): ResponseInterface
    {
        $employee = $this->managerEmployee();
        if ($employee === null) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não permitido.']);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $contract = (string) ($payload['contract'] ?? '');
        $mode = (string) ($payload['mode'] ?? 'approximate');
        if (! in_array($mode, ['approximate', 'exact'], true)) {
            $mode = 'approximate';
        }

        $builder = db_connect()->table('delivery_assignments da')
            ->select('da.contract, da.unit_id AS unitId, o.area_id AS areaId')
            ->join('orders o', 'o.contract = da.contract')
            ->where('da.contract', $contract);
        $this->applyScope($builder, $employee);
        if ($builder->get()->getRowArray() === null) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Atendimento fora do escopo.']);
        }

        $rawToken = bin2hex(random_bytes(32));
        $now = Time::now('UTC');
        $expiresAt = $now->addHours((int) config('TrackingPrivacy')->shareTokenTtlHours);
        $db = db_connect();
        $db->table('tracking_share_tokens')->insert([
            'contract' => $contract,
            'token_hash' => hash('sha256', $rawToken),
            'created_by_user_id' => auth()->user()->id,
            'expires_at' => $expiresAt->toDateTimeString(),
            'created_at' => $now->toDateTimeString(),
            'updated_at' => $now->toDateTimeString(),
        ]);
        $db->table('orders')->where('contract', $contract)->update([
            'public_tracking_mode' => $mode,
            'public_tracking_enabled' => 1,
            'updated_at' => $now->toDateTimeString(),
        ]);

        return $this->response->setJSON([
            'ok' => true,
            'url' => site_url('track/' . $rawToken),
            'expiresAt' => $expiresAt->toDateTimeString(),
            'mode' => $mode,
        ]);
    }

    public function revokeShare(): ResponseInterface
    {
        $employee = $this->managerEmployee();
        if ($employee === null) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não permitido.']);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $contract = (string) ($payload['contract'] ?? '');
        $builder = db_connect()->table('delivery_assignments da')
            ->select('da.contract, da.unit_id AS unitId, o.area_id AS areaId')
            ->join('orders o', 'o.contract = da.contract')
            ->where('da.contract', $contract);
        $this->applyScope($builder, $employee);
        if ($builder->get()->getRowArray() === null) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Atendimento fora do escopo.']);
        }

        $now = Time::now('UTC')->toDateTimeString();
        $db = db_connect();
        $db->table('tracking_share_tokens')->where('contract', $contract)->where('revoked_at', null)->update(['revoked_at' => $now, 'updated_at' => $now]);
        $db->table('orders')->where('contract', $contract)->update(['public_tracking_enabled' => 0, 'updated_at' => $now]);

        return $this->response->setJSON(['ok' => true]);
    }

    private function routeDistance(array $points): int
    {
        $distance = 0.0;
        for ($index = 1; $index < count($points); $index++) {
            $distance += TrackingPrivacy::distanceMeters(
                (float) $points[$index - 1]['latitude'],
                (float) $points[$index - 1]['longitude'],
                (float) $points[$index]['latitude'],
                (float) $points[$index]['longitude'],
            );
        }

        return (int) round($distance);
    }

    private function elapsedMinutes(?string $collectedAt, ?string $deliveredAt): ?int
    {
        if ($collectedAt === null) {
            return null;
        }

        $end = $deliveredAt === null ? Time::now('UTC') : Time::parse($deliveredAt, 'UTC');
        return max(0, (int) round(($end->getTimestamp() - Time::parse($collectedAt, 'UTC')->getTimestamp()) / 60));
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
