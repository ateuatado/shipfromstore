<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TrackingPrivacy;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

final class PublicTracking extends BaseController
{
    public function show(string $token): string|ResponseInterface
    {
        $db = db_connect();
        $now = Time::now('UTC');
        $share = $db->table('tracking_share_tokens st')
            ->select('st.id AS shareId, st.contract, st.expires_at AS expiresAt, o.status, o.collected_at AS collectedAt, o.delivered_at AS deliveredAt, o.delivery_lat AS deliveryLat, o.delivery_lng AS deliveryLng, o.public_tracking_mode AS trackingMode, da.courier_user_id AS courierUserId')
            ->join('orders o', 'o.contract = st.contract')
            ->join('delivery_assignments da', 'da.contract = st.contract')
            ->where('st.token_hash', hash('sha256', $token))
            ->where('st.revoked_at', null)
            ->where('st.expires_at >', $now->toDateTimeString())
            ->where('o.public_tracking_enabled', 1)
            ->get()->getRowArray();

        if ($share === null) {
            return $this->response->setStatusCode(404)->setBody('Link de rastreamento inválido, expirado ou revogado.');
        }

        $points = $db->table('delivery_tracking_points')
            ->select('latitude, longitude, accuracy_meters AS accuracy, sequence, recorded_at AS recordedAt')
            ->where('assignment_id', $db->table('delivery_assignments')->select('id')->where('contract', $share['contract'])->get()->getRow('id'))
            ->orderBy('sequence', 'ASC')
            ->get()->getResultArray();

        $privacy = new TrackingPrivacy();
        $order = $db->table('orders')->where('contract', $share['contract'])->get()->getRowArray();
        $points = $privacy->visiblePoints($points, (float) $order['pickup_lat'], (float) $order['pickup_lng'], (string) $share['trackingMode']);
        if ($share['trackingMode'] !== 'exact') {
            $points = $privacy->approximatePoints($points);
        }

        $db->table('tracking_share_tokens')->where('id', $share['shareId'])->update(['last_accessed_at' => $now->toDateTimeString()]);

        return view('pages/public_tracking', [
            'title' => 'Rastreamento de entrega | ShipFromStore',
            'tracking' => [
                'contract' => $share['contract'],
                'status' => $share['status'],
                'collectedAt' => $share['collectedAt'],
                'deliveredAt' => $share['deliveredAt'],
                'delivery' => ['lat' => (float) $share['deliveryLat'], 'lng' => (float) $share['deliveryLng']],
                'points' => $points,
                'privacyNotice' => $share['trackingMode'] === 'exact'
                    ? 'Rastreamento autorizado pelo cliente.'
                    : 'A origem da coleta está protegida. A rota é exibida somente após a área de segurança.',
            ],
        ]);
    }
}
