<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Map extends BaseConfig
{
    public array $center = [
        'lat' => -23.5294829,
        'lng' => -46.7386242,
        'label' => 'Rua Mergenthaler, 595',
    ];

    public int $radiusMeters = 15000;
    public int $slaSeconds = 7200;
    public int $pickupServiceSeconds = 600;
    public int $deliveryServiceSeconds = 300;
    public int $safetyMarginSeconds = 900;

    public array $areas = [
        ['id' => 'area-norte', 'name' => 'Área Norte', 'description' => 'Unidades da porção norte do território sintético.'],
        ['id' => 'area-sul', 'name' => 'Área Sul', 'description' => 'Unidades da porção sul do território sintético.'],
    ];

    /** Synthetic units distributed at deterministic random-looking points within the 15 km radius. */
    public array $units = [
        ['id' => 'CenEntEnc_01', 'name' => 'Centro 01', 'areaId' => 'area-norte', 'areaName' => 'Área Norte', 'lat' => -23.5106873, 'lng' => -46.7319633, 'capacity' => 3],
        ['id' => 'CenEntEnc_02', 'name' => 'Centro 02', 'areaId' => 'area-norte', 'areaName' => 'Área Norte', 'lat' => -23.5041382, 'lng' => -46.7005768, 'capacity' => 4],
        ['id' => 'CenEntEnc_03', 'name' => 'Centro 03', 'areaId' => 'area-norte', 'areaName' => 'Área Norte', 'lat' => -23.5305960, 'lng' => -46.6690708, 'capacity' => 2],
        ['id' => 'CenEntEnc_09', 'name' => 'Centro 09', 'areaId' => 'area-norte', 'areaName' => 'Área Norte', 'lat' => -23.4642176, 'lng' => -46.8073657, 'capacity' => 4],
        ['id' => 'CenEntEnc_10', 'name' => 'Centro 10', 'areaId' => 'area-norte', 'areaName' => 'Área Norte', 'lat' => -23.4020742, 'lng' => -46.7681617, 'capacity' => 2],
        ['id' => 'CenEntEnc_04', 'name' => 'Centro 04', 'areaId' => 'area-sul', 'areaName' => 'Área Sul', 'lat' => -23.5797603, 'lng' => -46.6658533, 'capacity' => 4],
        ['id' => 'CenEntEnc_05', 'name' => 'Centro 05', 'areaId' => 'area-sul', 'areaName' => 'Área Sul', 'lat' => -23.6282747, 'lng' => -46.7056815, 'capacity' => 3],
        ['id' => 'CenEntEnc_06', 'name' => 'Centro 06', 'areaId' => 'area-sul', 'areaName' => 'Área Sul', 'lat' => -23.6443774, 'lng' => -46.7867276, 'capacity' => 2],
        ['id' => 'CenEntEnc_07', 'name' => 'Centro 07', 'areaId' => 'area-sul', 'areaName' => 'Área Sul', 'lat' => -23.5470961, 'lng' => -46.7682057, 'capacity' => 5],
        ['id' => 'CenEntEnc_08', 'name' => 'Centro 08', 'areaId' => 'area-sul', 'areaName' => 'Área Sul', 'lat' => -23.5255978, 'lng' => -46.7992223, 'capacity' => 3],
    ];

    public array $samplePickup = ['lat' => -23.5752, 'lng' => -46.7088, 'label' => 'Coleta simulada'];
    public array $sampleDelivery = ['lat' => -23.5501, 'lng' => -46.7392, 'label' => 'Entrega simulada'];
}
