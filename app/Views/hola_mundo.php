<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>intergraphics · <?= esc($titulo) ?></title>
    <?= $this->include('partials/favicon') ?>
    <?= $this->include('partials/recursos_css') ?>
</head>
<body class="ig-panel">
    <div class="ig-franja"></div>
    <main class="container d-flex align-items-center justify-content-center py-5" style="min-height: calc(100vh - 6px);">
        <div class="card border-0 shadow-sm text-center p-4 p-md-5" style="max-width: 560px;">
            <img src="<?= base_url('assets/img/marca/colibri.png') ?>" alt="" height="64" class="mx-auto mb-3">
            <h1 class="h2 fw-bold text-ig-osc mb-2"><?= esc($mensaje) ?></h1>
            <p class="text-muted mb-4"><?= esc($sistema) ?></p>
            <div class="d-flex flex-wrap justify-content-center gap-2 small">
                <span class="badge rounded-pill ig-badge-rol">CodeIgniter <?= esc(\CodeIgniter\CodeIgniter::CI_VERSION) ?></span>
                <span class="badge rounded-pill ig-badge-rol">PHP <?= esc(PHP_VERSION) ?></span>
                <span class="badge rounded-pill ig-badge-rol">Bootstrap 5.3.3</span>
                <span class="badge rounded-pill ig-badge-invitado">Entorno: <?= esc(ENVIRONMENT) ?></span>
            </div>
        </div>
    </main>
    <?= $this->include('partials/recursos_js') ?>
</body>
</html>
