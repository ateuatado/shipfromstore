<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateEmployeesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 120],
            'matricula'  => ['type' => 'varchar', 'constraint' => 20],
            'role'       => ['type' => 'varchar', 'constraint' => 40],
            'unit_id'    => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'area_id'    => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('matricula');
        $this->forge->addKey('role');
        $this->forge->createTable('employees', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('employees', true);
    }
}
