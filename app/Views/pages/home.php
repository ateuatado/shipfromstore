<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="container-fluid py-3">
        <span class="badge text-bg-success mb-3">Ambiente pronto</span>
        <h1 class="display-5 fw-bold">Operação de loja em um só lugar.</h1>
        <p class="col-md-8 fs-5 text-secondary">Base inicial do ShipFromStore com CodeIgniter 4, autenticação Shield, Bootstrap e estrutura de assets separada.</p>
        <?php if (auth()->loggedIn()): ?>
            <a class="btn btn-primary btn-lg" href="<?= site_url('dashboard') ?>">Abrir painel</a>
        <?php else: ?>
            <a class="btn btn-primary btn-lg me-2" href="<?= site_url('register') ?>">Criar conta</a>
            <a class="btn btn-outline-primary btn-lg" href="<?= site_url('map') ?>">Ver mapa</a>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
