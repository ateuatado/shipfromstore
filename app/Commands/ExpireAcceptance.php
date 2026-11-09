<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\OrderLifecycle;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

final class ExpireAcceptance extends BaseCommand
{
    protected $group = 'ShipFromStore';
    protected $name = 'orders:expire-acceptance';
    protected $description = 'Reencaminha ao parceiro pedidos sem aceite após 15 minutos.';

    public function run(array $params)
    {
        $count = (new OrderLifecycle())->processAcceptanceTimeouts();
        CLI::write(sprintf('%d pedido(s) reencaminhado(s) ao parceiro.', $count), 'green');
    }
}
