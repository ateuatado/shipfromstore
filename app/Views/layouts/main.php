<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'ShipFromStore') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css?v=20261029-10') ?>">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= site_url('/') ?>">ShipFromStore</a>
            <div class="ms-auto">
                <?php if (auth()->loggedIn()): ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= site_url('logout') ?>">Sair</a>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="<?= site_url('login') ?>">Entrar</a>
                <?php endif ?>
            </div>
        </div>
    </nav>
    <main class="container py-4">
        <?= $this->renderSection('content') ?>
    </main>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
