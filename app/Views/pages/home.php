<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="home-hero p-4 p-lg-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="home-hero-content">
        <div class="home-hero-icon" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 14"></polyline></svg></div>
        <div>
            <span class="badge text-bg-success mb-2">Piloto SPIV</span>
            <h1 class="display-5 fw-bold mb-2">Entrega em até 2 horas.</h1>
            <p class="fs-5 text-secondary mb-0">Tecnologia: Correios</p>
        </div>
        <div class="home-hero-action">
        <?php if (auth()->loggedIn()): ?>
            <a class="btn btn-primary btn-lg" href="<?= site_url('dashboard') ?>">Abrir painel</a>
        <?php else: ?>
            <a class="btn btn-primary btn-lg me-2" href="<?= site_url('register') ?>">Criar conta</a>
            <a class="btn btn-outline-primary btn-lg" href="<?= site_url('map') ?>">Ver mapa</a>
        <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
