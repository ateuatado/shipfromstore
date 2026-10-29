<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateTrackingShareTokensTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'contract' => ['type' => 'varchar', 'constraint' => 60],
            'token_hash' => ['type' => 'char', 'constraint' => 64],
            'created_by_user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'expires_at' => ['type' => 'datetime'],
            'revoked_at' => ['type' => 'datetime', 'null' => true],
            'last_accessed_at' => ['type' => 'datetime', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addKey('contract');
        $this->forge->addKey('expires_at');
        $this->forge->createTable('tracking_share_tokens', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('tracking_share_tokens', true);
    }
}
