<?= $this->extend('layouts/panel') ?>

<?= $this->section('contenido') ?>
<div class="ig-bienvenida mb-4">
    <div>
        <span class="ig-subtitulo text-white-50">Bienvenido(a)</span>
        <h1 class="h3 fw-bold text-white mb-1"><?= esc($usuario['nombre']) ?></h1>
        <span class="badge rounded-pill bg-light text-dark"><i class="bi bi-shield-check me-1"></i><?= esc($usuario['rol']) ?></span>
    </div>
    <img src="<?= base_url('assets/img/marca/colibri.png') ?>" alt="" class="ig-bienvenida-colibri d-none d-sm-block">
</div>

<?php if ((int) $usuario['idrol'] === \App\Models\RolModel::INVITADO): ?>
    <div class="alert alert-warning">
        <i class="bi bi-hourglass-split me-1"></i>
        Su cuenta está activa como <strong>Invitado</strong>. Un administrador le asignará el rol correspondiente
        (Colaborador o Coordinador) para que pueda registrar vacaciones.
    </div>
<?php endif ?>

<?php if (($invitadosPendientes ?? 0) > 0): ?>
    <div class="alert ig-alerta-invitados d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span><i class="bi bi-person-exclamation me-1"></i>
            <strong><?= (int) $invitadosPendientes ?></strong> usuario(s) nuevo(s) esperan que les asigne un rol.</span>
        <a href="<?= site_url('panel/usuarios?rol=4&estado=activo') ?>" class="btn btn-sm btn-dark rounded-pill px-3">Asignar roles</a>
    </div>
<?php endif ?>

<div class="row g-4 mb-4">
    <?php
    $modulos = [
        ['permiso' => 'vacaciones.solicitar', 'icono' => 'bi-calendar-plus',   'titulo' => 'Solicitar vacaciones', 'texto' => 'Registre una nueva solicitud.', 'url' => 'panel/vacaciones/solicitar'],
        ['permiso' => 'vacaciones.ver_propias','icono' => 'bi-calendar-check',  'titulo' => 'Mis vacaciones',       'texto' => 'Consulte su saldo y el estado de sus solicitudes.', 'url' => 'panel/vacaciones'],
        ['permiso' => ['vacaciones.autorizar', 'vacaciones.ver_todas'], 'icono' => 'bi-clipboard-check', 'titulo' => 'Solicitudes del personal', 'texto' => 'Revise, autorice o rechace solicitudes.', 'url' => 'panel/solicitudes', 'contador' => true],
        ['permiso' => 'perfil.editar',        'icono' => 'bi-person-gear',     'titulo' => 'Mi perfil',            'texto' => 'Actualice sus datos y su contraseña.', 'url' => 'panel/perfil'],
        ['permiso' => 'reportes.ver',         'icono' => 'bi-bar-chart',       'titulo' => 'Reportes',             'texto' => 'Resumen de vacaciones del personal.', 'url' => 'panel/reportes'],
        ['permiso' => ['usuarios.ver', 'usuarios.gestionar'], 'icono' => 'bi-people', 'titulo' => 'Usuarios', 'texto' => 'Consulte al personal, asigne roles y active cuentas.', 'url' => 'panel/usuarios'],
        ['permiso' => 'cargos.gestionar',     'icono' => 'bi-briefcase',       'titulo' => 'Cargos',               'texto' => 'Administre los cargos del personal.', 'url' => 'panel/cargos'],
        ['permiso' => 'feriados.gestionar',   'icono' => 'bi-calendar-event',  'titulo' => 'Feriados',             'texto' => 'Feriados y días no laborables del año.', 'url' => 'panel/feriados'],
        ['permiso' => 'roles.gestionar',      'icono' => 'bi-key',             'titulo' => 'Roles y permisos',     'texto' => 'Configure el acceso por rol.', 'url' => 'panel/roles'],
    ];
    ?>
    <?php foreach ($modulos as $m): ?>
        <?php if (array_filter((array) $m['permiso'], 'tiene_permiso') !== []): ?>
            <div class="col-sm-6 col-lg-4">
                <?php $tag = isset($m['url']) ? 'a' : 'div'; ?>
                <<?= $tag ?> class="ig-modulo h-100 <?= isset($m['url']) ? 'ig-modulo-link' : '' ?>" <?= isset($m['url']) ? 'href="' . site_url($m['url']) . '"' : '' ?>>
                    <span class="ig-modulo-icono"><i class="bi <?= esc($m['icono']) ?>"></i></span>
                    <div>
                        <h2 class="h6 fw-semibold mb-1"><?= esc($m['titulo']) ?>
                            <?php if (! empty($m['contador']) && ig_pendientes_autorizar() > 0): ?>
                                <span class="badge rounded-pill ig-badge-cont ms-1"><?= ig_pendientes_autorizar() ?> pendiente<?= ig_pendientes_autorizar() === 1 ? '' : 's' ?></span>
                            <?php endif ?></h2>
                        <p class="small text-muted mb-1"><?= esc($m['texto']) ?></p>
                        <?php if (isset($m['url'])): ?>
                            <span class="small fw-semibold text-ig-osc">Abrir <i class="bi bi-arrow-right"></i></span>
                        <?php else: ?>
                            <span class="badge text-bg-light">Próximamente</span>
                        <?php endif ?>
                    </div>
                </<?= $tag ?>>
            </div>
        <?php endif ?>
    <?php endforeach ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h2 class="h6 fw-semibold mb-3"><i class="bi bi-key me-1 text-ig-osc"></i>Permisos de su rol</h2>
        <ul class="list-unstyled row row-cols-1 row-cols-md-2 g-2 mb-0 small">
            <?php foreach ($permisos as $p): ?>
                <li class="col"><i class="bi bi-check2 text-success me-1"></i><?= esc($p['descripcion']) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
</div>
<?= $this->endSection() ?>
