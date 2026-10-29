<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddOperationalTimingToOrders extends Migration
{
    public function up(): void
    {
        // The pilot database may already contain some of these columns because
        // the previous deployment partially applied this migration.
        $columns = [];

        if (! $this->db->fieldExists('acceptance_deadline_at', 'orders')) {
            $columns['acceptance_deadline_at'] = ['type' => 'datetime', 'null' => true];
        }

        if (! $this->db->fieldExists('sla_deadline_at', 'orders')) {
            $columns['sla_deadline_at'] = ['type' => 'datetime', 'null' => true];
        }

        if (! $this->db->fieldExists('partner_forwarded_at', 'orders')) {
            $columns['partner_forwarded_at'] = ['type' => 'datetime', 'null' => true];
        }

        if (! $this->db->fieldExists('partner_name', 'orders')) {
            $columns['partner_name'] = ['type' => 'varchar', 'constraint' => 120, 'null' => true];
        }

        if ($columns !== []) {
            $this->forge->addColumn('orders', $columns);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['acceptance_deadline_at', 'sla_deadline_at', 'partner_forwarded_at', 'partner_name']);
    }
}
