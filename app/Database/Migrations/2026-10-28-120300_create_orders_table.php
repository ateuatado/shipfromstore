<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'contract'        => ['type' => 'varchar', 'constraint' => 60],
            'customer_name'   => ['type' => 'varchar', 'constraint' => 120],
            'unit_id'         => ['type' => 'varchar', 'constraint' => 30],
            'area_id'         => ['type' => 'varchar', 'constraint' => 30],
            'pickup_address' => ['type' => 'varchar', 'constraint' => 255],
            'delivery_address'=> ['type' => 'varchar', 'constraint' => 255],
            'status'          => ['type' => 'varchar', 'constraint' => 40],
            'received_at'     => ['type' => 'datetime'],
            'accepted_at'     => ['type' => 'datetime', 'null' => true],
            'collected_at'    => ['type' => 'datetime', 'null' => true],
            'delivered_at'    => ['type' => 'datetime', 'null' => true],
            'created_at'      => ['type' => 'datetime', 'null' => true],
            'updated_at'      => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('contract');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('area_id');
        $this->forge->addKey('status');
        $this->forge->createTable('orders', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('orders', true);
    }
}
