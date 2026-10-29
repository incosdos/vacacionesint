<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->include('partials/titulo') ?>
    <meta name="description" content="<?= esc($empresa->razonSocial) ?>: diseño gráfico, producción de anuncios, revistas, banners digitales, sitios web y video. Outsourcing gráfico desde Cochabamba, Bolivia.">
    <?= $this->include('partials/favicon') ?>

    <?= $this->include('partials/recursos_css') ?>
    <?= $this->renderSection('estilos') ?>
</head>
<body data-bs-spy="scroll" data-bs-target="#navPrincipal" data-bs-offset="90" tabindex="0">

    <?= $this->include('partials/navbar') ?>

    <main>
        <?= $this->renderSection('contenido') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <?= $this->include('partials/recursos_js') ?>
    <script src="<?= base_url('assets/js/intergraphics.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
