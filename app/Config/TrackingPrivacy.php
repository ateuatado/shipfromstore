<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

final class TrackingPrivacy extends BaseConfig
{
    public int $pickupMaskRadiusMeters = 500;
    public int $shareTokenTtlHours = 24;
    public int $publicLocationPrecision = 3;
}
