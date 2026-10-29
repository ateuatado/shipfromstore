<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Percursos dos entregadores</h1><p class="text-secondary mb-0">Visualização limitada a <?= esc($employee['role'] === 'gestor_unidade' ? $employee['unit_id'] : ($employee['role'] === 'gestor_area' ? $employee['area_id'] : 'todo o território')) ?>.</p></div>
    <a class="btn btn-outline-primary" href="<?= site_url('dashboard') ?>">Voltar ao dashboard</a>
</div>
<div class="row g-3">
    <div class="col-lg-8"><div id="routes-map" class="route-map rounded shadow-sm" aria-label="Mapa dos percursos dos entregadores"></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Atendimentos</h2><button id="routes-refresh" class="btn btn-sm btn-outline-primary">Atualizar</button></div><div id="routes-summary" class="small text-secondary mb-3">Carregando percursos...</div><div id="routes-list" class="list-group list-group-flush"></div></div></div></div>
</div>
<link rel="stylesheet" href="<?= base_url('assets/leaflet/leaflet.css') ?>">
<script>window.SHIPFROMSTORE_ROUTES = {dataUrl: <?= json_encode(site_url('dashboard/routes/data')) ?>};</script>
<script src="<?= base_url('assets/leaflet/leaflet.js') ?>"></script>
<script src="<?= base_url('assets/js/routes.js?v=20261029-2') ?>"></script>
<?= $this->endSection() ?>
