<footer class="ig-footer">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <img src="<?= base_url('assets/img/marca/logo-intergraphics-blanco.png') ?>" alt="<?= esc($empresa->nombreComercial) ?>" class="ig-logo-blanco mb-2">
                <div>
                <small><?= esc($empresa->razonSocial) ?></small></div>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="<?= esc($empresa->redes['facebook']) ?>" class="ig-social" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="<?= esc($empresa->redes['instagram']) ?>" class="ig-social" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="<?= esc($empresa->redes['linkedin']) ?>" class="ig-social" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="text-center small mb-0">
            &copy; <?= date('Y') ?> <?= esc($empresa->razonSocial) ?> · Todos los derechos reservados.
        </p>
    </div>
</footer>
