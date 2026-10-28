<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddRouteCoordinatesToOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'pickup_lat' => ['type' => 'decimal', 'constraint' => '10,7', 'null' => true],
            'pickup_lng' => ['type' => 'decimal', 'constraint' => '10,7', 'null' => true],
            'delivery_lat' => ['type' => 'decimal', 'constraint' => '10,7', 'null' => true],
            'delivery_lng' => ['type' => 'decimal', 'constraint' => '10,7', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['pickup_lat', 'pickup_lng', 'delivery_lat', 'delivery_lng']);
    }
}
