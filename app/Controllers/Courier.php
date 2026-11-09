<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

final class Courier extends BaseController
{
    public function confirmCollection(): ResponseInterface
    {
        $payload = $this->request->getPost();
        $contract = (string) ($payload['contract'] ?? '');
        $photo = $this->request->getFile('photo');
        $assignment = $this->assignment($contract);

        if ($assignment === null || $assignment['status'] !== 'assigned') {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Coleta não está disponível para este entregador.']);
        }

        if ($photo === null || ! $photo->isValid() || $photo->hasMoved() || ! str_starts_with((string) $photo->getMimeType(), 'image/')) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'A foto do objeto é obrigatória.']);
        }

        if ($photo->getSizeByUnit('mb') > 8) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'A foto deve ter no máximo 8 MB.']);
        }

        $binary = file_get_contents($photo->getTempName());
        if ($binary === false) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Não foi possível ler a foto enviada.']);
        }
        $hash = hash('sha256', $binary);
        $directory = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'photos';
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        $extension = match ($photo->getMimeType()) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
        $filename = $hash . '.' . $extension;
        $photo->move($directory, $filename, true);

        $now = Time::now('UTC')->toDateTimeString();
        $slaDeadline = Time::now('UTC')->addMinutes((int) config('Operational')->deliveryWindowMinutes)->toDateTimeString();
        $db = db_connect();
        $db->transStart();
        $db->table('delivery_assignments')->where('id', $assignment['assignment_id'])->update([
            'status' => 'collected',
            'collection_confirmed_at' => $now,
            'collection_photo_path' => 'uploads/photos/' . $filename,
            'collection_photo_hash' => $hash,
            'updated_at' => $now,
        ]);
        $db->table('orders')->where('contract', $contract)->update([
            'status' => 'collected',
            'collected_at' => $now,
            'sla_deadline_at' => $slaDeadline,
            'updated_at' => $now,
        ]);
        $db->transComplete();

        return $this->response->setJSON(['ok' => true, 'collectedAt' => $now, 'slaDeadlineAt' => $slaDeadline, 'slaSeconds' => 7200]);
    }

    public function confirmDelivery(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? [];
        $contract = (string) ($payload['contract'] ?? '');
        $assignment = $this->assignment($contract);

        if ($assignment === null || ! in_array($assignment['status'], ['collected', 'in_transit'], true)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Entrega não está disponível para este entregador.']);
        }

        $now = Time::now('UTC')->toDateTimeString();
        $db = db_connect();
        $db->transStart();
        $db->table('delivery_assignments')->where('id', $assignment['assignment_id'])->update(['status' => 'delivered', 'delivered_at' => $now, 'updated_at' => $now]);
        $db->table('orders')->where('contract', $contract)->update(['status' => 'delivered', 'delivered_at' => $now, 'updated_at' => $now]);
        $db->transComplete();

        return $this->response->setJSON(['ok' => true, 'deliveredAt' => $now]);
    }

    public function recordTrackingPoint(): ResponseInterface
    {
        $payload = $this->request->getJSON(true) ?? [];
        $contract = (string) ($payload['contract'] ?? '');
        $latitude = filter_var($payload['latitude'] ?? null, FILTER_VALIDATE_FLOAT);
        $longitude = filter_var($payload['longitude'] ?? null, FILTER_VALIDATE_FLOAT);
        $accuracy = filter_var($payload['accuracy'] ?? null, FILTER_VALIDATE_FLOAT);
        $assignment = $this->assignment($contract);

        if ($assignment === null || ! in_array($assignment['status'], ['collected', 'in_transit'], true)) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Rastreamento não está disponível para este atendimento.']);
        }

        if ($latitude === false || $longitude === false || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Coordenadas de localização inválidas.']);
        }

        if ($accuracy !== false && ($accuracy < 0 || $accuracy > 10000)) {
            $accuracy = null;
        }

        $db = db_connect();
        $sequence = (int) $db->table('delivery_tracking_points')
            ->where('assignment_id', $assignment['assignment_id'])
            ->countAllResults() + 1;
        $now = Time::now('UTC')->toDateTimeString();
        $db->table('delivery_tracking_points')->insert([
            'assignment_id' => $assignment['assignment_id'],
            'courier_user_id' => auth()->user()->id,
            'unit_id' => $assignment['unit_id'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy_meters' => $accuracy,
            'sequence' => $sequence,
            'recorded_at' => $now,
            'created_at' => $now,
        ]);

        return $this->response->setJSON(['ok' => true, 'recordedAt' => $now, 'sequence' => $sequence]);
    }

    private function assignment(string $contract): ?array
    {
        $user = auth()->user();
        $employee = db_connect()->table('employees')->where('user_id', $user->id)->get()->getRowArray();
        if (($employee['role'] ?? null) !== 'entregador') {
            return null;
        }

        return db_connect()->table('delivery_assignments da')
            ->select('da.id AS assignment_id, da.status, da.unit_id')
            ->where('da.contract', $contract)
            ->where('da.courier_user_id', $user->id)
            ->where('da.unit_id', $employee['unit_id'])
            ->get()->getRowArray() ?: null;
    }
}
