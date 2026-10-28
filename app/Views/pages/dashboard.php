<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$statusLabels = ['pending_acceptance' => 'Pendentes de aceite', 'accepted' => 'Aceitos', 'collected' => 'Em entrega', 'delivered' => 'Entregues'];
$statusClasses = ['pending_acceptance' => 'warning', 'accepted' => 'info', 'collected' => 'primary', 'delivered' => 'success'];
$orderedStatuses = ['pending_acceptance', 'accepted', 'collected', 'delivered'];
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Pedidos</h1><p class="text-secondary mb-0">Olá, <?= esc($employee['name'] ?? auth()->user()->username) ?>. Acompanhe a operação do seu escopo.</p></div>
    <div class="btn-group" role="group" aria-label="Navegação operacional"><a class="btn btn-primary" href="<?= site_url('dashboard') ?>">Pedidos</a><a class="btn btn-outline-primary" href="<?= site_url('dashboard/pickups') ?>">Coletas</a><a class="btn btn-outline-primary" href="<?= site_url('dashboard/deliveries') ?>">Entregues</a></div>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($orderedStatuses as $status): ?><div class="col-sm-6 col-xl-3"><div class="card h-100 shadow-sm"><div class="card-body"><div class="text-secondary small"><?= esc($statusLabels[$status]) ?></div><div class="display-6 fw-semibold"><?= count(array_filter($orders, static fn (array $order): bool => $order['status'] === $status)) ?></div></div></div></div><?php endforeach ?>
</div>
<div class="card shadow-sm"><div class="card-body"><h2 class="h5 mb-3">Acompanhamento dos pedidos</h2>
<?php foreach ($orderedStatuses as $status): $group = array_values(array_filter($orders, static fn (array $order): bool => $order['status'] === $status)); ?>
    <section class="mb-4" aria-labelledby="status-<?= esc($status) ?>"><h3 id="status-<?= esc($status) ?>" class="h6 border-bottom pb-2"><span class="badge text-bg-<?= esc($statusClasses[$status]) ?> me-2"><?= count($group) ?></span><?= esc($statusLabels[$status]) ?></h3>
    <?php if ($group === []): ?><p class="text-secondary small mb-0">Nenhum pedido nesta etapa.</p><?php else: ?><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Contrato</th><th>Nome</th><th>Unidade</th><th>Endereço de coleta</th></tr></thead><tbody><?php foreach ($group as $order): ?><tr><td class="fw-semibold"><?= esc($order['contract']) ?></td><td><?= esc($order['customerName']) ?></td><td><?= esc($order['unitId']) ?></td><td><?= esc($order['pickupAddress']) ?></td></tr><?php endforeach ?></tbody></table></div><?php endif ?></section>
<?php endforeach ?>
</div></div>
<?= $this->endSection() ?>
