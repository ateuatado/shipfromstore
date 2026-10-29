<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

final class Operational extends BaseConfig
{
    public int $acceptanceWindowMinutes = 15;
    public int $deliveryWindowMinutes = 120;
    public string $partnerName = 'Parceiro logístico';
}
