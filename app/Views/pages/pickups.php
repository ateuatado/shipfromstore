<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 mb-1">Coletas</h1><p class="text-secondary mb-0">Endereços de coleta recebidos e aceitos pelo seu escopo.</p></div><a class="btn btn-outline-primary" href="<?= site_url('dashboard') ?>">Voltar aos pedidos</a></div>
<div class="card shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Contrato</th><th>Nome</th><th>Unidade</th><th>Endereço de coleta</th><th>Status</th></tr></thead><tbody>
<?php $pickupOrders = array_values(array_filter($orders, static fn (array $order): bool => in_array($order['status'], ['assigned', 'collected', 'in_transit'], true))); ?>
<?php if ($pickupOrders === []): ?><tr><td colspan="5" class="text-secondary">Nenhuma coleta disponível.</td></tr><?php else: foreach ($pickupOrders as $order): ?><tr><td class="fw-semibold"><?= esc($order['contract']) ?></td><td><?= esc($order['customerName']) ?></td><td><?= esc($order['unitId']) ?></td><td><?= esc($order['pickupAddress']) ?></td><td><span class="badge text-bg-<?= in_array($order['status'], ['collected', 'in_transit'], true) ? 'primary' : 'info' ?>"><?= esc(['assigned' => 'Atribuído', 'collected' => 'Coletado', 'in_transit' => 'Em entrega'][$order['status']]) ?></span></td></tr><?php endforeach; endif ?>
</tbody></table></div></div></div>
<?= $this->endSection() ?>
