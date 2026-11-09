<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddPackageDimensionsToOrders extends Migration
{
    public function up(): void
    {
        $columns = [];
        foreach ([
            'package_height_cm' => ['type' => 'decimal', 'constraint' => '8,2', 'null' => true],
            'package_width_cm' => ['type' => 'decimal', 'constraint' => '8,2', 'null' => true],
            'package_depth_cm' => ['type' => 'decimal', 'constraint' => '8,2', 'null' => true],
            'package_weight_kg' => ['type' => 'decimal', 'constraint' => '8,3', 'null' => true],
        ] as $name => $definition) {
            if (! $this->db->fieldExists($name, 'orders')) {
                $columns[$name] = $definition;
            }
        }
        if ($columns !== []) {
            $this->forge->addColumn('orders', $columns);
        }
    }

    public function down(): void
    {
        foreach (['package_height_cm', 'package_width_cm', 'package_depth_cm', 'package_weight_kg'] as $column) {
            if ($this->db->fieldExists($column, 'orders')) {
                $this->forge->dropColumn('orders', $column);
            }
        }
    }
}
