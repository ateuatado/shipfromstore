<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-1">Mapa de viabilidade</h1>
        <p class="text-secondary mb-0">Raio sintético de 15 km a partir de <?= esc($center['label']) ?>, com unidades fictícias.</p>
    </div>
    <a class="btn btn-outline-primary" href="<?= site_url('/') ?>">Início</a>
</div>
<div class="row g-4">
    <div class="col-lg-8"><div id="shipfromstore-map" class="map-panel rounded shadow-sm" aria-label="Mapa da operação simulada"></div></div>
    <div class="col-lg-4">
        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h5">Ordem simulada</h2>
            <dl class="row small mb-0">
                <dt class="col-5">Coleta</dt><dd class="col-7"><?= esc($pickup['label']) ?></dd>
                <dt class="col-5">Entrega</dt><dd class="col-7"><?= esc($delivery['label']) ?></dd>
                <dt class="col-5">SLA</dt><dd class="col-7">120 minutos após a coleta</dd>
                <dt class="col-5">Regra</dt><dd class="col-7">15 min operação + 15 min margem</dd>
            </dl>
            <hr>
            <div id="route-status" class="small text-secondary">Selecione uma unidade para calcular a rota.</div>
        </div></div>
        <div class="card shadow-sm mt-3"><div class="card-body">
            <h2 class="h5">Unidades fictícias</h2>
            <div id="area-summary" class="small text-secondary mb-2"></div>
            <div id="unit-list" class="list-group list-group-flush"></div>
        </div></div>
    </div>
</div>
<link rel="stylesheet" href="<?= base_url('assets/leaflet/leaflet.css') ?>">
<script>
window.SHIPFROMSTORE_MAP = <?= json_encode([
    'units' => $units,
    'areas' => $areas,
    'center' => $center,
    'radiusMeters' => $radiusMeters,
    'pickup' => $pickup,
    'delivery' => $delivery,
    'routeUrl' => site_url('api/maps/route'),
], JSON_THROW_ON_ERROR) ?>;
</script>
<script src="<?= base_url('assets/leaflet/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/map.js') ?>"></script>
<?= $this->endSection() ?>
