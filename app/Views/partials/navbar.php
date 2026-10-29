<nav class="navbar navbar-expand-lg fixed-top ig-navbar" id="navPrincipal">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>#inicio">
            <?= $this->include('partials/logo') ?>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal" aria-controls="menuPrincipal"
                aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>#inicio">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>#proyectos">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>#contacto">Contacto</a></li>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="btn btn-ig px-4" href="<?= site_url('login') ?>">
                        <i class="bi bi-person-circle me-1"></i> Iniciar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
