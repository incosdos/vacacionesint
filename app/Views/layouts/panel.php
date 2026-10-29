<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->include('partials/titulo') ?>
    <?= $this->include('partials/favicon') ?>
    <?= $this->include('partials/recursos_css') ?>
</head>
<body class="ig-panel">
<nav class="navbar navbar-expand-lg ig-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('panel') ?>"><?= $this->include('partials/logo') ?></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPanel"
                aria-controls="menuPanel" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPanel">
            <?php $seccion ??= 'panel'; ?>
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= $seccion === 'panel' ? 'active' : '' ?>" href="<?= site_url('panel') ?>"><i class="bi bi-grid me-1"></i>Panel</a></li>
                <?php if (tiene_permiso('vacaciones.ver_propias')): ?>
                    <li class="nav-item"><a class="nav-link <?= $seccion === 'vacaciones' ? 'active' : '' ?>" href="<?= site_url('panel/vacaciones') ?>"><i class="bi bi-sun me-1"></i>Mis vacaciones</a></li>
                <?php endif ?>
                <?php if (tiene_permiso('vacaciones.ver_todas') || tiene_permiso('vacaciones.autorizar')): ?>
                    <li class="nav-item"><a class="nav-link <?= $seccion === 'solicitudes' ? 'active' : '' ?>" href="<?= site_url('panel/solicitudes') ?>"><i class="bi bi-clipboard-check me-1"></i>Solicitudes
                        <?php if (($pend = ig_pendientes_autorizar()) > 0): ?><span class="badge rounded-pill ig-badge-cont ms-1"><?= $pend ?></span><?php endif ?></a></li>
                <?php endif ?>
                <?php if (tiene_permiso('reportes.ver')): ?>
                    <li class="nav-item"><a class="nav-link <?= $seccion === 'reportes' ? 'active' : '' ?>" href="<?= site_url('panel/reportes') ?>"><i class="bi bi-bar-chart me-1"></i>Reportes</a></li>
                <?php endif ?>
                <?php
                $admin = array_filter([
                    (tiene_permiso('usuarios.ver') || tiene_permiso('usuarios.gestionar')) ? ['usuarios', 'panel/usuarios', 'bi-people', 'Usuarios'] : null,
                    tiene_permiso('cargos.gestionar') ? ['cargos', 'panel/cargos', 'bi-briefcase', 'Cargos'] : null,
                    tiene_permiso('feriados.gestionar') ? ['feriados', 'panel/feriados', 'bi-calendar-event', 'Feriados'] : null,
                    tiene_permiso('roles.gestionar') ? ['roles', 'panel/roles', 'bi-shield-lock', 'Roles y permisos'] : null,
                ]);
                $enAdmin = in_array($seccion, array_column($admin, 0), true);
                ?>
                <?php if (count($admin) === 1): ?>
                    <?php [$sec, $url, $ico, $txt] = array_values($admin)[0]; ?>
                    <li class="nav-item"><a class="nav-link <?= $enAdmin ? 'active' : '' ?>" href="<?= site_url($url) ?>"><i class="bi <?= $ico ?> me-1"></i><?= $txt ?></a></li>
                <?php elseif ($admin !== []): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $enAdmin ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-1"></i>Administración</a>
                        <ul class="dropdown-menu shadow-sm">
                            <?php foreach ($admin as [$sec, $url, $ico, $txt]): ?>
                                <li><a class="dropdown-item <?= $seccion === $sec ? 'active' : '' ?>" href="<?= site_url($url) ?>"><i class="bi <?= $ico ?> me-2"></i><?= $txt ?></a></li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                <?php endif ?>
            </ul>
            <div class="dropdown">
                <button class="btn btn-light rounded-pill dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="ig-avatar"><?= esc(mb_strtoupper(mb_substr(session('nombre') ?? '?', 0, 1))) ?></span>
                    <span class="text-start lh-sm">
                        <span class="d-block small fw-semibold"><?= esc(session('nombre')) ?></span>
                        <span class="d-block text-muted" style="font-size:.75rem"><?= esc(session('rol')) ?></span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><span class="dropdown-item-text small text-muted"><?= esc(session('email')) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <?php if (tiene_permiso('perfil.editar')): ?>
                        <li><a class="dropdown-item <?= ($seccion ?? '') === 'perfil' ? 'active' : '' ?>" href="<?= site_url('panel/perfil') ?>"><i class="bi bi-person-gear me-2"></i>Mi perfil</a></li>
                    <?php endif ?>
                    <li><a class="dropdown-item" href="<?= site_url('/') ?>"><i class="bi bi-house me-2"></i>Sitio web</a></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('salir') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4 py-md-5">
    <?= $this->include('partials/alertas') ?>
    <?= $this->renderSection('contenido') ?>
</main>

<footer class="text-center small text-muted py-4">
    &copy; <?= date('Y') ?> <?= esc($empresa->razonSocial) ?>
</footer>

<?= $this->include('partials/recursos_js') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
