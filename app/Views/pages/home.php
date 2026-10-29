<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="container-fluid py-3">
        <span class="badge text-bg-success mb-3">Piloto ShipFromStore</span>
        <h1 class="display-5 fw-bold">Entrega em até duas horas.</h1>
        <p class="col-md-8 fs-5 text-secondary">Tecnologia: Correios</p>
        <?php if (auth()->loggedIn()): ?>
            <a class="btn btn-primary btn-lg" href="<?= site_url('dashboard') ?>">Abrir painel</a>
        <?php else: ?>
            <a class="btn btn-primary btn-lg me-2" href="<?= site_url('register') ?>">Criar conta</a>
            <a class="btn btn-outline-primary btn-lg" href="<?= site_url('map') ?>">Ver mapa</a>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
