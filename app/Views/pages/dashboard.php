<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$statusLabels = ['pending_acceptance' => 'Pendentes de aceite', 'partner_forwarded' => 'Encaminhados ao parceiro', 'assigned' => 'Atribuídos', 'collected' => 'Coleta confirmada', 'in_transit' => 'Em entrega', 'delivered' => 'Entregues'];
$statusClasses = ['pending_acceptance' => 'warning', 'partner_forwarded' => 'danger', 'assigned' => 'info', 'collected' => 'primary', 'in_transit' => 'primary', 'delivered' => 'success'];
$orderedStatuses = ['pending_acceptance', 'partner_forwarded', 'assigned', 'collected', 'in_transit', 'delivered'];
$pendingAcceptance = array_values(array_filter($orders, static fn (array $order): bool => $order['status'] === 'pending_acceptance'));
$partnerForwarded = array_values(array_filter($orders, static fn (array $order): bool => $order['status'] === 'partner_forwarded'));
$mapUnits = config('Map')->units;
$mapUnitsById = [];
foreach ($mapUnits as $mapUnit) {
    $mapUnitsById[$mapUnit['id']] = $mapUnit;
}
$mapData = array_map(static function (array $order) use ($mapUnitsById): array {
    $unit = $mapUnitsById[$order['unitId']] ?? null;
    return [
        'collectionId' => $order['collectionId'] ?? $order['contract'],
        'contract' => $order['contract'],
        'status' => $order['status'],
        'pickup' => ['lat' => (float) $order['pickupLat'], 'lng' => (float) $order['pickupLng'], 'label' => $order['pickupAddress']],
        'delivery' => ['lat' => (float) $order['deliveryLat'], 'lng' => (float) $order['deliveryLng'], 'label' => $order['deliveryAddress']],
        'unit' => $unit === null ? null : ['id' => $unit['id'], 'lat' => (float) $unit['lat'], 'lng' => (float) $unit['lng'], 'areaName' => $unit['areaName']],
    ];
}, $mapOrders ?? $orders);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Pedidos</h1><p class="text-secondary mb-0">Olá, <?= esc($employee['name'] ?? auth()->user()->username) ?>. Acompanhe a operação do seu escopo.</p></div>
    <div class="dashboard-nav btn-group" role="group" aria-label="Navegação operacional"><a class="btn btn-primary" href="<?= site_url('dashboard') ?>">Pedidos</a><a class="btn btn-outline-primary" href="<?= site_url('dashboard/pickups') ?>">Coletas</a><a class="btn btn-outline-primary" href="<?= site_url('dashboard/deliveries') ?>">Entregues</a><a class="btn btn-outline-primary" href="<?= site_url('dashboard/routes') ?>">Percursos</a><?php if (($employee['role'] ?? null) === 'gestor_unidade'): ?><a class="btn btn-outline-primary" href="<?= site_url('dashboard/assignments') ?>">Atribuir</a><?php endif ?></div>
</div>
<?php if ($pendingAcceptance !== []): ?><div class="alert alert-warning operational-alert" role="alert"><strong>Atenção:</strong> <?= count($pendingAcceptance) ?> pedido(s) aguardam aceite da unidade. O prazo termina em <span data-countdown-deadline="<?= esc($pendingAcceptance[0]['acceptanceDeadlineAt'] ?? '') ?>">carregando...</span>.</div><?php endif ?>
<?php if ($partnerForwarded !== []): ?><div class="alert alert-danger operational-alert" role="alert"><strong>Reencaminhamento:</strong> <?= count($partnerForwarded) ?> pedido(s) ultrapassaram 15 minutos sem aceite e foram enviados ao parceiro.</div><?php endif ?>
<div class="row g-3 mb-4 dashboard-metrics">
    <?php foreach ($orderedStatuses as $status): ?><div class="col-6 col-sm-6 col-xl-3"><div class="card h-100 shadow-sm"><div class="card-body"><div class="text-secondary small"><?= esc($statusLabels[$status]) ?></div><div class="display-6 fw-semibold"><?= count(array_filter($orders, static fn (array $order): bool => $order['status'] === $status)) ?></div></div></div></div><?php endforeach ?>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card shadow-sm h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Fluxo operacional</h2><span class="small text-secondary">Seu escopo</span></div><div class="status-bars"><?php foreach ($orderedStatuses as $status): $count = count(array_filter($orders, static fn (array $order): bool => $order['status'] === $status)); $width = $metrics['total'] > 0 ? max(3, (int) round(($count / $metrics['total']) * 100)) : 3; ?><div class="status-bar-row"><span><?= esc($statusLabels[$status]) ?></span><div class="status-bar"><i class="bg-<?= esc($statusClasses[$status]) ?>" style="width: <?= $width ?>%"></i></div><strong><?= $count ?></strong></div><?php endforeach ?></div></div></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-body"><h2 class="h5 mb-3">Indicadores de prazo</h2><div class="metric-highlight"><span>Dentro de 2 horas</span><strong><?= $metrics['slaRate'] === null ? '—' : esc($metrics['slaRate']) . '%' ?></strong></div><div class="metric-highlight"><span>Tempo médio após coleta</span><strong><?= $metrics['averageMinutes'] === null ? '—' : esc($metrics['averageMinutes']) . ' min' ?></strong></div><div class="metric-highlight"><span>Percursos ativos</span><strong><?= esc($metrics['active']) ?></strong></div></div></div></div>
</div>
<div class="card shadow-sm mb-4"><div class="card-body"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><div><h2 class="h5 mb-1">Mapa operacional</h2><p class="text-secondary small mb-0">Unidade → coleta → entrega, dentro do escopo permitido.</p></div><span class="small text-secondary"><span class="badge rounded-pill text-bg-primary">●</span> unidade <span class="badge rounded-pill text-bg-success">●</span> coleta <span class="badge rounded-pill text-bg-danger">●</span> entrega</span></div><div id="dashboard-map" class="map-panel rounded" aria-label="Mapa operacional dos pedidos"></div></div></div>
<div class="card shadow-sm"><div class="card-body"><h2 class="h5 mb-3">Acompanhamento dos pedidos</h2>
<?php foreach ($orderedStatuses as $status): $group = array_values(array_filter($orders, static fn (array $order): bool => $order['status'] === $status)); ?>
    <section class="mb-4" aria-labelledby="status-<?= esc($status) ?>"><h3 id="status-<?= esc($status) ?>" class="h6 border-bottom pb-2"><span class="badge text-bg-<?= esc($statusClasses[$status]) ?> me-2"><?= count($group) ?></span><?= esc($statusLabels[$status]) ?></h3>
    <?php if ($group === []): ?><p class="text-secondary small mb-0">Nenhum pedido nesta etapa.</p><?php else: ?><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Contrato</th><th>Nome</th><th>Unidade</th><th>Endereço de coleta</th></tr></thead><tbody><?php foreach ($group as $order): ?><tr><td class="fw-semibold"><?= esc($order['contract']) ?></td><td><?= esc($order['customerName']) ?></td><td><?= esc($order['unitId']) ?></td><td><?= esc($order['pickupAddress']) ?></td></tr><?php endforeach ?></tbody></table></div><?php endif ?></section>
<?php endforeach ?>
</div></div>
<link rel="stylesheet" href="<?= base_url('assets/leaflet/leaflet.css') ?>">
<script>window.SHIPFROMSTORE_DASHBOARD_MAP = <?= json_encode(['orders' => $mapData], JSON_THROW_ON_ERROR) ?>;</script>
<script src="<?= base_url('assets/leaflet/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/dashboard-map.js?v=20261029-6') ?>"></script>
<?= $this->endSection() ?>
