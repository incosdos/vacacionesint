<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->include('partials/titulo') ?>
    <?= $this->include('partials/favicon') ?>
    <?= $this->include('partials/recursos_css') ?>
</head>
<body class="ig-login">
<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- Panel de imagen (oculto en móviles) -->
        <div class="col-lg-5 col-xl-6 d-none d-lg-flex ig-login-panel" style="background-image:url('<?= ig_img($imagenPanel ?? 'banner2') ?>')">
            <img src="<?= base_url('assets/img/marca/logo-intergraphics-blanco.png') ?>" alt="<?= esc($empresa->nombreComercial) ?>" class="ig-logo-blanco ig-login-panel-logo">
            <div class="ig-login-panel-texto">
                <h2 class="fw-bold text-white">Sistema interno</h2>
                <p class="text-white-50 mb-0">Registro de vacaciones, permisos y autorizaciones del personal de <?= esc($empresa->razonSocial) ?></p>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-lg-7 col-xl-6 d-flex align-items-center justify-content-center py-5 px-3">
            <div class="ig-login-caja w-100 <?= esc($anchoCaja ?? '') ?>">
                <a href="<?= site_url('/') ?>" class="d-inline-block mb-4">
                    <?= $this->include('partials/logo') ?>
                </a>

                <?= $this->include('partials/alertas') ?>

                <?= $this->renderSection('contenido') ?>

                <p class="text-center small text-muted mt-4 mb-0">
                    <a href="<?= site_url('/') ?>"><i class="bi bi-arrow-left"></i> Volver al inicio</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Botones para mostrar u ocultar contraseñas
document.querySelectorAll('[data-ver-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const campo = document.getElementById(btn.dataset.verPassword);
        const oculto = campo.type === 'password';
        campo.type = oculto ? 'text' : 'password';
        btn.querySelector('i').className = oculto ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
});
</script>
<?= $this->include('partials/recursos_js') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
