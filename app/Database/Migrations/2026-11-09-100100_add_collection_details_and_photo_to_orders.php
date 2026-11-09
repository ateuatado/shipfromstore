<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddCollectionDetailsAndPhotoToOrders extends Migration
{
    public function up(): void
    {
        $orderColumns = [];
        foreach ([
            'company_name' => ['type' => 'varchar', 'constraint' => 120, 'null' => true],
            'pickup_contact_name' => ['type' => 'varchar', 'constraint' => 120, 'null' => true],
            'recipient_name' => ['type' => 'varchar', 'constraint' => 120, 'null' => true],
        ] as $name => $definition) {
            if (! $this->db->fieldExists($name, 'orders')) {
                $orderColumns[$name] = $definition;
            }
        }
        if ($orderColumns !== []) {
            $this->forge->addColumn('orders', $orderColumns);
        }

        $assignmentColumns = [];
        foreach ([
            'collection_photo_path' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'collection_photo_hash' => ['type' => 'char', 'constraint' => 64, 'null' => true],
        ] as $name => $definition) {
            if (! $this->db->fieldExists($name, 'delivery_assignments')) {
                $assignmentColumns[$name] = $definition;
            }
        }
        if ($assignmentColumns !== []) {
            $this->forge->addColumn('delivery_assignments', $assignmentColumns);
        }
    }

    public function down(): void
    {
        foreach (['company_name', 'pickup_contact_name', 'recipient_name'] as $column) {
            if ($this->db->fieldExists($column, 'orders')) {
                $this->forge->dropColumn('orders', $column);
            }
        }
        foreach (['collection_photo_path', 'collection_photo_hash'] as $column) {
            if ($this->db->fieldExists($column, 'delivery_assignments')) {
                $this->forge->dropColumn('delivery_assignments', $column);
            }
        }
    }
}
