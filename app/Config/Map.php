<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Map extends BaseConfig
{
    /** Synthetic operational units for the Moema–Jaguaré prototype zone. */
    public array $units = [
        ['id' => 'CenEntEnc_01', 'name' => 'Centro 01', 'lat' => -23.6008, 'lng' => -46.6622, 'capacity' => 3],
        ['id' => 'CenEntEnc_02', 'name' => 'Centro 02', 'lat' => -23.5924, 'lng' => -46.6801, 'capacity' => 4],
        ['id' => 'CenEntEnc_03', 'name' => 'Centro 03', 'lat' => -23.5831, 'lng' => -46.6978, 'capacity' => 2],
        ['id' => 'CenEntEnc_04', 'name' => 'Centro 04', 'lat' => -23.5737, 'lng' => -46.7155, 'capacity' => 4],
        ['id' => 'CenEntEnc_05', 'name' => 'Centro 05', 'lat' => -23.5632, 'lng' => -46.7312, 'capacity' => 3],
        ['id' => 'CenEntEnc_06', 'name' => 'Centro 06', 'lat' => -23.5516, 'lng' => -46.7476, 'capacity' => 2],
        ['id' => 'CenEntEnc_07', 'name' => 'Centro 07', 'lat' => -23.5788, 'lng' => -46.7479, 'capacity' => 5],
        ['id' => 'CenEntEnc_08', 'name' => 'Centro 08', 'lat' => -23.5664, 'lng' => -46.7008, 'capacity' => 3],
        ['id' => 'CenEntEnc_09', 'name' => 'Centro 09', 'lat' => -23.5495, 'lng' => -46.7167, 'capacity' => 4],
        ['id' => 'CenEntEnc_10', 'name' => 'Centro 10', 'lat' => -23.5911, 'lng' => -46.7338, 'capacity' => 2],
    ];

    public array $samplePickup = ['lat' => -23.5752, 'lng' => -46.7088, 'label' => 'Coleta simulada'];
    public array $sampleDelivery = ['lat' => -23.5501, 'lng' => -46.7392, 'label' => 'Entrega simulada'];
}
