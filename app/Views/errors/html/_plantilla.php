<?php
/**
 * Plantilla común de páginas de error (Bootstrap 5 y marca INTERGRAPHICS).
 * Variables: $codigo, $titulo, $texto, $icono, $detalle (solo fuera de producción).
 */
helper('url');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>intergraphics · <?= esc($titulo) ?></title>
    <link rel="icon" href="<?= base_url('favicon.ico') . '?v=' . (is_file(FCPATH . 'favicon.ico') ? filemtime(FCPATH . 'favicon.ico') : '1') ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/marca/colibri.png') ?>">
    <link href="<?= base_url('assets/vendor/poppins/poppins.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/intergraphics.css') ?>" rel="stylesheet">
</head>
<body class="ig-panel">
    <div class="ig-franja"></div>
    <main class="container d-flex align-items-center justify-content-center py-5" style="min-height: calc(100vh - 6px);">
        <div class="text-center" style="max-width: 560px;">
            <a href="<?= base_url() ?>" class="d-inline-block mb-4">
                <img src="<?= base_url('assets/img/marca/logo-intergraphics.png') ?>" alt="intergraphics" height="44">
            </a>
            <div class="ig-error-codigo"><?= esc((string) $codigo) ?></div>
            <h1 class="h3 fw-bold mb-2"><i class="bi <?= esc($icono) ?> text-ig-osc me-2"></i><?= esc($titulo) ?></h1>
            <p class="text-muted mb-4"><?= esc($texto) ?></p>

            <?php if (! empty($detalle) && ENVIRONMENT !== 'production'): ?>
                <div class="alert alert-light border text-start small mb-4"><strong>Detalle (modo desarrollo):</strong><br><?= nl2br(esc($detalle)) ?></div>
            <?php endif ?>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="<?= base_url() ?>" class="btn btn-ig px-4"><i class="bi bi-house me-1"></i>Ir al inicio</a>
                <a href="<?= base_url('panel') ?>" class="btn btn-outline-dark rounded-pill px-4"><i class="bi bi-grid me-1"></i>Ir al panel</a>
                <button type="button" class="btn btn-light rounded-pill px-4" onclick="history.back()"><i class="bi bi-arrow-left me-1"></i>Volver</button>
            </div>
            <p class="small text-muted mt-5 mb-0">International Graphics Outsourcing S.R.L.</p>
        </div>
    </main>
</body>
</html>
