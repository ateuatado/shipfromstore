<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TrackingPrivacy;
use CodeIgniter\Test\CIUnitTestCase;

final class TrackingPrivacyTest extends CIUnitTestCase
{
    public function testMasksPointsInsideThePickupRadius(): void
    {
        $service = new TrackingPrivacy();
        $points = [
            ['latitude' => -23.5294829, 'longitude' => -46.7386242, 'sequence' => 1],
            ['latitude' => -23.5260, 'longitude' => -46.7386, 'sequence' => 2],
            ['latitude' => -23.5200, 'longitude' => -46.7386, 'sequence' => 3],
        ];

        $visible = $service->visiblePoints($points, -23.5294829, -46.7386242);

        $this->assertCount(1, $visible);
        $this->assertSame(3, $visible[0]['sequence']);
    }

    public function testExactModeKeepsAllPoints(): void
    {
        $service = new TrackingPrivacy();
        $points = [
            ['latitude' => -23.5294829, 'longitude' => -46.7386242],
            ['latitude' => -23.5260, 'longitude' => -46.7386],
        ];

        $this->assertCount(2, $service->visiblePoints($points, -23.5294829, -46.7386242, 'exact'));
    }
}
