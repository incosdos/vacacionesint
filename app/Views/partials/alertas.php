<?php if (session('exito')): ?>
    <div class="alert alert-success py-2"><i class="bi bi-check-circle me-1"></i><?= esc(session('exito')) ?></div>
<?php endif ?>
<?php if (session('info')): ?>
    <div class="alert alert-info py-2">
        <i class="bi bi-info-circle me-1"></i><?= esc(session('info')) ?>
        <?php if (session('enlace_dev')): ?>
            <a href="<?= esc(session('enlace_dev'), 'attr') ?>" class="d-block mt-2 text-break fw-semibold">Confirmar mi correo</a>
        <?php endif ?>
    </div>
<?php endif ?>
<?php if (session('error')): ?>
    <div class="alert alert-danger py-2">
        <i class="bi bi-exclamation-circle me-1"></i><?= esc(session('error')) ?>
        <?php if (session('mostrar_reenviar')): ?>
            <a href="<?= site_url('registro/reenviar') ?>" class="d-block mt-1">Reenviar correo de confirmación</a>
        <?php endif ?>
    </div>
<?php endif ?>
<?php if (session('errores')): ?>
    <div class="alert alert-danger py-2">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i>Revise los siguientes datos:</div>
        <ul class="mb-0 ps-3 small">
            <?php foreach (session('errores') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>
