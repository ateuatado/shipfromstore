<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddCollectionIdToOrders extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('collection_id', 'orders')) {
            $this->forge->addColumn('orders', [
                'collection_id' => ['type' => 'varchar', 'constraint' => 80, 'null' => true],
            ]);
        }

        // Existing records represented one collection each before concurrent
        // unit offers were introduced. Preserve that meaning during migration.
        $this->db->query('UPDATE orders SET collection_id = contract WHERE collection_id IS NULL');
        $this->forge->addKey('collection_id', 'orders_collection_id_index');
    }

    public function down(): void
    {
        if ($this->db->fieldExists('collection_id', 'orders')) {
            $this->forge->dropColumn('orders', 'collection_id');
        }
    }
}
