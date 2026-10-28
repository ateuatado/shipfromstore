<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$labels = ['assigned' => 'Atribuída', 'collected' => 'Coleta confirmada', 'in_transit' => 'Em entrega', 'delivered' => 'Entregue'];
$classes = ['assigned' => 'warning', 'collected' => 'info', 'in_transit' => 'primary', 'delivered' => 'success'];
?>
<div class="mb-4"><h1 class="h2 mb-1">Minhas coletas</h1><p class="text-secondary mb-0">Olá, <?= esc($employee['name']) ?>. Estas são as coletas atribuídas a você.</p></div>
<div class="alert alert-primary" role="status">Você verá somente atendimentos atribuídos à sua matrícula e à sua unidade.</div>
<?php if ($orders === []): ?><div class="card shadow-sm"><div class="card-body text-secondary">Nenhuma coleta ativa atribuída no momento.</div></div><?php else: ?>
    <div class="row g-4">
    <?php foreach ($orders as $order): ?><div class="col-12"><article class="card shadow-sm courier-order" data-contract="<?= esc($order['contract']) ?>" data-status="<?= esc($order['status']) ?>" data-collected-at="<?= esc($order['collectedAt'] ?? '') ?>" data-pickup-lat="<?= esc($order['pickupLat'] ?? '') ?>" data-pickup-lng="<?= esc($order['pickupLng'] ?? '') ?>" data-delivery-lat="<?= esc($order['deliveryLat'] ?? '') ?>" data-delivery-lng="<?= esc($order['deliveryLng'] ?? '') ?>"><div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3"><div><span class="badge text-bg-<?= esc($classes[$order['status']] ?? 'secondary') ?> js-status"><?= esc($labels[$order['status']] ?? $order['status']) ?></span><h2 class="h5 mt-2 mb-0"><?= esc($order['contract']) ?></h2></div><span class="text-secondary small"><?= esc($order['unitId']) ?></span></div>
        <dl class="row mb-3"><dt class="col-sm-3">Contato na coleta</dt><dd class="col-sm-9"><?= esc($order['customerName']) ?></dd><dt class="col-sm-3">Endereço de coleta</dt><dd class="col-sm-9"><?= esc($order['pickupAddress']) ?></dd><dt class="col-sm-3">Destino</dt><dd class="col-sm-9"><?= esc($order['deliveryAddress']) ?></dd></dl>
        <?php if ($order['status'] === 'assigned'): ?><div class="border rounded p-3 js-signature-panel"><p class="fw-semibold mb-2">Assinatura do contato no recebimento</p><canvas class="w-100 border rounded js-signature" height="180" aria-label="Área para assinatura do contato"></canvas><div class="d-flex justify-content-between mt-2"><button type="button" class="btn btn-outline-secondary btn-sm js-clear-signature">Limpar</button><button type="button" class="btn btn-primary js-confirm-collection">Confirmar recebimento</button></div><div class="small text-danger mt-2 js-error" role="alert"></div></div><?php elseif (in_array($order['status'], ['collected', 'in_transit'], true)): ?><div class="text-center border rounded p-3"><div class="text-secondary small">Prazo restante</div><div class="display-3 fw-semibold text-primary js-countdown">--:--:--</div><p class="text-secondary small mb-3">A contagem começou quando a coleta foi confirmada.</p><a class="btn btn-primary js-route" target="_blank" rel="noopener">Abrir rota até a entrega</a><button type="button" class="btn btn-success ms-2 js-confirm-delivery">Confirmar entrega</button></div><?php else: ?><div class="alert alert-success mb-0">Entrega concluída em <?= esc($order['deliveredAt']) ?>.</div><?php endif ?>
    </div></article></div><?php endforeach ?>
    </div>
<?php endif ?>
<?php if (($completedOrders ?? []) !== []): ?>
<div class="mt-5"><h2 class="h5 mb-3">Histórico de entregas</h2><div class="row g-3">
<?php foreach ($completedOrders as $order): ?><div class="col-12"><article class="card border-success-subtle"><div class="card-body"><div class="d-flex flex-wrap justify-content-between gap-2 mb-2"><div><span class="badge text-bg-success">Entregue</span><h2 class="h6 mt-2 mb-0"><?= esc($order['contract']) ?></h2></div><span class="text-secondary small"><?= esc($order['unitId']) ?></span></div><div class="small text-secondary">Entrega concluída em <?= esc($order['deliveredAt']) ?>.</div></div></article></div><?php endforeach ?>
</div></div>
<?php endif ?>
<script>window.SHIPFROMSTORE_COURIER = {collectionUrl: <?= json_encode(site_url('courier/collection/confirm')) ?>, deliveryUrl: <?= json_encode(site_url('courier/delivery/confirm')) ?>};</script>
<script src="<?= base_url('assets/js/courier.js') ?>"></script>
<?= $this->endSection() ?>
