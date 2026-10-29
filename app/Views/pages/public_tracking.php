<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Rastreamento da entrega</h1><p class="text-secondary mb-0">Atendimento <?= esc($tracking['contract']) ?></p></div>
</div>
<div class="row g-3">
    <div class="col-lg-8"><div id="public-tracking-map" class="route-map rounded shadow-sm" aria-label="Mapa do rastreamento da entrega"></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-body">
        <h2 class="h5 mb-3">Status da entrega</h2>
        <p class="mb-2"><strong><?= esc(['collected' => 'Coleta confirmada', 'in_transit' => 'Em entrega', 'delivered' => 'Entrega concluída'][$tracking['status']] ?? 'Em processamento') ?></strong></p>
        <?php if ($tracking['collectedAt'] !== null): ?><p class="small text-secondary mb-2">Coleta confirmada em <?= esc($tracking['collectedAt']) ?>.</p><?php endif ?>
        <?php if ($tracking['deliveredAt'] !== null): ?><p class="small text-secondary mb-2">Entrega concluída em <?= esc($tracking['deliveredAt']) ?>.</p><?php endif ?>
        <div class="alert alert-info small mb-0"><?= esc($tracking['privacyNotice']) ?></div>
    </div></div></div>
</div>
<link rel="stylesheet" href="<?= base_url('assets/leaflet/leaflet.css') ?>">
<script>window.SHIPFROMSTORE_PUBLIC_TRACKING = <?= json_encode($tracking, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="<?= base_url('assets/leaflet/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/public-tracking.js?v=20261029-1') ?>"></script>
<?= $this->endSection() ?>
