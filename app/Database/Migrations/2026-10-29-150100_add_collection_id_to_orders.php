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
        // The column is intentionally enough for the pilot. Do not recreate
        // an index when a previous partial deployment already created it.
    }

    public function down(): void
    {
        if ($this->db->fieldExists('collection_id', 'orders')) {
            $this->forge->dropColumn('orders', 'collection_id');
        }
    }
}
