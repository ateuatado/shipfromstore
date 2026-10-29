<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddTrackingPrivacyToOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'pickup_privacy_mode' => ['type' => 'varchar', 'constraint' => 30, 'default' => 'masked_500m'],
            'public_tracking_mode' => ['type' => 'varchar', 'constraint' => 20, 'default' => 'approximate'],
            'public_tracking_enabled' => ['type' => 'tinyint', 'constraint' => 1, 'default' => 0],
        ]);
        $this->db->query('UPDATE orders SET pickup_privacy_mode = \'masked_500m\', public_tracking_mode = \'approximate\', public_tracking_enabled = 0');
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['pickup_privacy_mode', 'public_tracking_mode', 'public_tracking_enabled']);
    }
}
