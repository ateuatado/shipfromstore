<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

final class Orders extends BaseConfig
{
    public array $sample = [
        ['contract' => 'CT-2026-0001', 'customerName' => 'Cliente Demonstração 01', 'unitId' => 'CenEntEnc_01', 'areaId' => 'area-norte', 'pickupAddress' => 'Rua Coriolano, 120 — Vila Leopoldina', 'status' => 'pending_acceptance', 'receivedAt' => '2026-10-28 12:05'],
        ['contract' => 'CT-2026-0002', 'customerName' => 'Cliente Demonstração 02', 'unitId' => 'CenEntEnc_03', 'areaId' => 'area-norte', 'pickupAddress' => 'Rua Cerro Corá, 245 — Alto da Lapa', 'status' => 'pending_acceptance', 'receivedAt' => '2026-10-28 12:12'],
        ['contract' => 'CT-2026-0003', 'customerName' => 'Cliente Demonstração 03', 'unitId' => 'CenEntEnc_04', 'areaId' => 'area-sul', 'pickupAddress' => 'Avenida Jabaquara, 360 — Saúde', 'status' => 'accepted', 'receivedAt' => '2026-10-28 11:40', 'acceptedAt' => '2026-10-28 11:47'],
        ['contract' => 'CT-2026-0004', 'customerName' => 'Cliente Demonstração 04', 'unitId' => 'CenEntEnc_07', 'areaId' => 'area-sul', 'pickupAddress' => 'Rua Tito, 510 — Vila Romana', 'status' => 'collected', 'receivedAt' => '2026-10-28 10:55', 'acceptedAt' => '2026-10-28 11:02', 'collectedAt' => '2026-10-28 11:20'],
        ['contract' => 'CT-2026-0005', 'customerName' => 'Cliente Demonstração 05', 'unitId' => 'CenEntEnc_09', 'areaId' => 'area-norte', 'pickupAddress' => 'Rua Guaicui, 88 — Pinheiros', 'status' => 'delivered', 'receivedAt' => '2026-10-28 09:20', 'acceptedAt' => '2026-10-28 09:25', 'collectedAt' => '2026-10-28 09:42', 'deliveredAt' => '2026-10-28 10:48'],
        ['contract' => 'CT-2026-0006', 'customerName' => 'Cliente Demonstração 06', 'unitId' => 'CenEntEnc_10', 'areaId' => 'area-norte', 'pickupAddress' => 'Rua Harmonia, 210 — Vila Madalena', 'status' => 'delivered', 'receivedAt' => '2026-10-28 08:50', 'acceptedAt' => '2026-10-28 08:56', 'collectedAt' => '2026-10-28 09:10', 'deliveredAt' => '2026-10-28 10:14'],
    ];
}
