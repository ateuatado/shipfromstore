<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateDeliveryAssignmentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'contract'              => ['type' => 'varchar', 'constraint' => 60],
            'unit_id'               => ['type' => 'varchar', 'constraint' => 30],
            'courier_user_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'assigned_by_user_id'   => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'status'                => ['type' => 'varchar', 'constraint' => 40, 'default' => 'assigned'],
            'assigned_at'           => ['type' => 'datetime'],
            'collection_confirmed_at' => ['type' => 'datetime', 'null' => true],
            'delivered_at'          => ['type' => 'datetime', 'null' => true],
            'signature_path'        => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'signature_hash'        => ['type' => 'char', 'constraint' => 64, 'null' => true],
            'created_at'            => ['type' => 'datetime', 'null' => true],
            'updated_at'            => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('contract');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('courier_user_id');
        $this->forge->addKey('assigned_by_user_id');
        $this->forge->addKey('status');
        $this->forge->createTable('delivery_assignments', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('delivery_assignments', true);
    }
}
