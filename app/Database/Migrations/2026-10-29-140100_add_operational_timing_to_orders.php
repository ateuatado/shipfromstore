<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddOperationalTimingToOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'acceptance_deadline_at' => ['type' => 'datetime', 'null' => true],
            'sla_deadline_at' => ['type' => 'datetime', 'null' => true],
            'partner_forwarded_at' => ['type' => 'datetime', 'null' => true],
            'partner_name' => ['type' => 'varchar', 'constraint' => 120, 'null' => true],
        ]);
        $this->forge->addKey('acceptance_deadline_at', 'orders_acceptance_deadline_index');
        $this->forge->addKey('sla_deadline_at', 'orders_sla_deadline_index');
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', ['acceptance_deadline_at', 'sla_deadline_at', 'partner_forwarded_at', 'partner_name']);
    }
}
