<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h2 mb-1">Atribuir coleta</h1><p class="text-secondary mb-0"><?= esc($employee['name']) ?> · <?= esc($employee['unit_id']) ?></p></div><a class="btn btn-outline-primary" href="<?= site_url('dashboard') ?>">Voltar aos pedidos</a></div>
<?php if (session('message')): ?><div class="alert alert-success" role="status"><?= esc(session('message')) ?></div><?php endif ?>
<?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
<div class="card shadow-sm"><div class="card-body"><h2 class="h5 mb-3">Pedidos aguardando aceite</h2>
<?php if ($orders === []): ?><p class="text-secondary mb-0">Não há pedidos pendentes para esta unidade.</p><?php else: ?><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Contrato</th><th>Contato</th><th>Endereço de coleta</th><th>Ação</th></tr></thead><tbody>
<?php foreach ($orders as $order): ?><tr><td class="fw-semibold"><?= esc($order['contract']) ?></td><td><?= esc($order['customer_name']) ?></td><td><?= esc($order['pickup_address']) ?></td><td><form method="post" action="<?= site_url('dashboard/assignments') ?>" class="d-flex gap-2"><?= csrf_field() ?><input type="hidden" name="contract" value="<?= esc($order['contract']) ?>"><select class="form-select form-select-sm" name="courier_user_id" required aria-label="Entregador para <?= esc($order['contract']) ?>"><option value="">Selecione</option><?php foreach ($couriers as $courier): ?><option value="<?= esc($courier['user_id']) ?>"><?= esc($courier['name']) ?> · <?= esc($courier['matricula']) ?></option><?php endforeach ?></select><button class="btn btn-primary btn-sm text-nowrap" type="submit">Aceitar e atribuir</button></form></td></tr><?php endforeach ?>
</tbody></table></div><?php endif ?></div></div>
<div class="alert alert-info mt-4" role="note">A atribuição só pode ser feita para entregadores vinculados a esta unidade. O horário de aceite e de atribuição será registrado pelo servidor.</div>
<?= $this->endSection() ?>
