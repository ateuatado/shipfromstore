<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$labels = ['assigned' => 'Atribuída', 'collected' => 'Coleta confirmada', 'in_transit' => 'Em entrega', 'delivered' => 'Entregue'];
$classes = ['assigned' => 'warning', 'collected' => 'info', 'in_transit' => 'primary', 'delivered' => 'success'];
?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h2 mb-1">Minhas coletas</h1><p class="text-secondary mb-0">Olá, <?= esc($employee['name']) ?>. Estas são as coletas atribuídas a você.</p></div></div>
<div class="alert alert-primary" role="status">Você verá somente atendimentos atribuídos à sua matrícula e à sua unidade.</div>
<?php if ($orders === []): ?><div class="card shadow-sm"><div class="card-body text-secondary">Nenhuma coleta atribuída no momento.</div></div><?php else: ?>
    <div class="row g-4">
    <?php foreach ($orders as $order): ?><div class="col-12"><article class="card shadow-sm"><div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><div><span class="badge text-bg-<?= esc($classes[$order['status']] ?? 'secondary') ?>"><?= esc($labels[$order['status']] ?? $order['status']) ?></span><h2 class="h5 mt-2 mb-0"><?= esc($order['contract']) ?></h2></div><span class="text-secondary small"><?= esc($order['unitId']) ?></span></div>
        <dl class="row mb-0"><dt class="col-sm-3">Contato na coleta</dt><dd class="col-sm-9"><?= esc($order['customerName']) ?></dd><dt class="col-sm-3">Endereço de coleta</dt><dd class="col-sm-9"><?= esc($order['pickupAddress']) ?></dd><dt class="col-sm-3">Destino</dt><dd class="col-sm-9"><?= esc($order['deliveryAddress']) ?></dd></dl>
        <?php if ($order['status'] === 'collected' || $order['status'] === 'in_transit'): ?><div class="display-6 fw-semibold text-primary mt-3" aria-label="Prazo de entrega em andamento">120 min</div><p class="text-secondary small mb-0">O prazo começa após a confirmação da coleta.</p><?php endif ?>
    </div></article></div><?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
