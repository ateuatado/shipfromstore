<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Simulador operacional</h1><p class="text-secondary mb-0">Dispare pedidos sintéticos e acompanhe o aceite das unidades.</p></div>
    <a class="btn btn-outline-primary" href="<?= site_url('logout') ?>">Sair</a>
</div>
<?php if (session('message')): ?><div class="alert alert-success" role="status"><?= esc(session('message')) ?></div><?php endif ?>
<?php if (session('error')): ?><div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h5 mb-3">Novo pedido simulado</h2>
            <form method="post" action="<?= site_url('simulator') ?>" data-simulator-form data-random-location-url="<?= esc(site_url('simulator/random-location')) ?>">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label" for="company_name">Nome da empresa</label><input class="form-control" id="company_name" name="company_name" required value="Zara"></div>
                <div class="mb-3"><label class="form-label" for="pickup_contact_name">Nome do contato na coleta</label><input class="form-control" id="pickup_contact_name" name="pickup_contact_name" required value="Contato Zara"></div>
                <div class="mb-3"><label class="form-label" for="recipient_name">Nome do destinatário</label><input class="form-control" id="recipient_name" name="recipient_name" required value="Destinatário simulado"></div>
                <div class="mb-3"><label class="form-label">Dimensões do pacote (cm) e peso (kg)</label><div class="row g-2"><div class="col-3"><input class="form-control" type="number" min="1" step="0.01" name="package_height_cm" value="20" required aria-label="Altura em centímetros"><small class="text-secondary">Altura</small></div><div class="col-3"><input class="form-control" type="number" min="1" step="0.01" name="package_width_cm" value="30" required aria-label="Largura em centímetros"><small class="text-secondary">Largura</small></div><div class="col-3"><input class="form-control" type="number" min="1" step="0.01" name="package_depth_cm" value="15" required aria-label="Profundidade em centímetros"><small class="text-secondary">Prof.</small></div><div class="col-3"><input class="form-control" type="number" min="0.001" step="0.001" name="package_weight_kg" value="2" required aria-label="Peso em quilogramas"><small class="text-secondary">Peso kg</small></div></div></div>
                <div class="mb-3"><div class="d-flex justify-content-between align-items-center gap-2"><label class="form-label mb-0" for="pickup_address">Endereço de coleta</label><button class="btn btn-outline-primary btn-sm" type="button" data-random-location="pickup">Sortear endereço</button></div><input class="form-control mt-2" id="pickup_address" name="pickup_address" required readonly value="Rua Mergenthaler, 595 — Vila Leopoldina, São Paulo/SP"><input type="hidden" id="pickup_lat" name="pickup_lat" value="-23.5294829"><input type="hidden" id="pickup_lng" name="pickup_lng" value="-46.7386242"></div>
                <div class="mb-3"><div class="d-flex justify-content-between align-items-center gap-2"><label class="form-label mb-0" for="delivery_address">Endereço de entrega</label><button class="btn btn-outline-primary btn-sm" type="button" data-random-location="delivery">Sortear endereço</button></div><input class="form-control mt-2" id="delivery_address" name="delivery_address" required readonly value="Avenida Ibirapuera, 2120 — Moema, São Paulo/SP"><input type="hidden" id="delivery_lat" name="delivery_lat" value="-23.6015"><input type="hidden" id="delivery_lng" name="delivery_lng" value="-46.6653"></div>
                <button class="btn btn-primary w-100" type="submit">Disparar pedido</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm"><div class="card-body"><h2 class="h5 mb-3">Últimas ofertas às unidades</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Coleta</th><th>Unidade</th><th>Status</th><th>Entrada</th><th>Prazo de aceite</th></tr></thead><tbody>
        <?php if ($orders === []): ?><tr><td colspan="5" class="text-secondary">Nenhuma coleta simulada.</td></tr><?php else: foreach ($orders as $order): ?><tr><td class="fw-semibold"><?= esc($order['collection_id'] ?? $order['contract']) ?></td><td><?= esc($order['unit_id']) ?></td><td><span class="badge text-bg-<?= $order['status'] === 'pending_acceptance' ? 'warning' : ($order['status'] === 'partner_forwarded' ? 'danger' : ($order['status'] === 'offer_cancelled' ? 'secondary' : 'success')) ?>"><?= esc(['pending_acceptance' => 'Aguardando aceite', 'partner_forwarded' => 'Encaminhado ao parceiro', 'offer_cancelled' => 'Oferta encerrada'][$order['status']] ?? $order['status']) ?></span></td><td><?= esc($order['received_at']) ?></td><td><?= esc($order['acceptance_deadline_at'] ?? '—') ?></td></tr><?php endforeach; endif ?>
        </tbody></table></div></div></div>
        <div class="alert alert-info mt-3">Cada coleta é um único pedido. O sistema envia ofertas para as unidades mais próximas; quando uma aceita, as demais ofertas são encerradas automaticamente.</div>
        <div class="card border-danger mt-3"><div class="card-body"><h2 class="h6 text-danger">Limpar demonstração</h2><p class="small text-secondary">Apaga pedidos, ofertas, atribuições, percursos, pontos GPS, links de rastreamento e fotos. Os usuários e as configurações permanecem.</p><form method="post" action="<?= site_url('simulator/reset') ?>" onsubmit="return confirm('Apagar todos os registros operacionais da demonstração? Esta ação não pode ser desfeita.');"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Apagar registros da demonstração</button></form></div></div>
    </div>
</div>
<?= $this->endSection() ?>
