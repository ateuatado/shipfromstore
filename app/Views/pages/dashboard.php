<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h2 mb-1">Painel</h1><p class="text-secondary mb-0">Bem-vindo, <?= esc(auth()->user()->username ?? 'usuário') ?>.</p></div>
</div>
<div class="row g-4">
    <div class="col-md-4"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Pedidos</h2><p class="text-secondary">Acompanhe pedidos e status de atendimento.</p></div></div></div>
    <div class="col-md-4"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Estoque</h2><p class="text-secondary">Estrutura pronta para múltiplas lojas e disponibilidade.</p></div></div></div>
    <div class="col-md-4"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Conta</h2><p class="text-secondary">Acesso protegido pelo CodeIgniter Shield.</p></div></div></div>
</div>
<?= $this->endSection() ?>
