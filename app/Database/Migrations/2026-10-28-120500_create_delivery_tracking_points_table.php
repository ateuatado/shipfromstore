<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateDeliveryTrackingPointsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'bigint', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'assignment_id'   => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'courier_user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'unit_id'         => ['type' => 'varchar', 'constraint' => 30],
            'latitude'        => ['type' => 'decimal', 'constraint' => '10,7'],
            'longitude'       => ['type' => 'decimal', 'constraint' => '10,7'],
            'accuracy_meters' => ['type' => 'decimal', 'constraint' => '8,2', 'null' => true],
            'sequence'        => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'recorded_at'     => ['type' => 'datetime'],
            'created_at'      => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('assignment_id');
        $this->forge->addKey('courier_user_id');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('recorded_at');
        $this->forge->createTable('delivery_tracking_points', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('delivery_tracking_points', true);
    }
}
