<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 mb-1">Pedidos entregues</h1><p class="text-secondary mb-0">Histórico de entregas dentro do escopo permitido.</p></div><a class="btn btn-outline-primary" href="<?= site_url('dashboard') ?>">Voltar aos pedidos</a></div>
<div class="card shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Contrato</th><th>Nome</th><th>Unidade</th><th>Entrega concluída</th></tr></thead><tbody>
<?php $deliveredOrders = array_values(array_filter($orders, static fn (array $order): bool => $order['status'] === 'delivered')); ?>
<?php if ($deliveredOrders === []): ?><tr><td colspan="4" class="text-secondary">Nenhum pedido entregue.</td></tr><?php else: foreach ($deliveredOrders as $order): ?><tr><td class="fw-semibold"><?= esc($order['contract']) ?></td><td><?= esc($order['customerName']) ?></td><td><?= esc($order['unitId']) ?></td><td><?= esc($order['deliveredAt']) ?></td></tr><?php endforeach; endif ?>
</tbody></table></div></div></div>
<?= $this->endSection() ?>
