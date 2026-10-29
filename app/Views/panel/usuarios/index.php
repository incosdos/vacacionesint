<?= $this->extend('layouts/panel') ?>

<?= $this->section('contenido') ?>
<?= $this->setVar('migas', ['Panel' => 'panel', 'Usuarios' => null])->include('partials/migas') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Usuarios</h1>
        <p class="text-muted mb-0">Personal registrado en el sistema de vacaciones.</p>
    </div>
    <?php if (tiene_permiso('usuarios.gestionar')): ?>
        <a href="<?= site_url('panel/usuarios/nuevo') ?>" class="btn btn-ig px-3"><i class="bi bi-person-plus me-1"></i> Nuevo usuario</a>
    <?php endif ?>
</div>

<!-- Indicadores (también filtran) -->
<div class="row g-3 mb-4">
    <?php
    $tarjetas = [
        ['',          'Total',                 $stats['total'] ?? 0,      'bi-people',               ''],
        ['activo',    'Activos',               $stats['activos'] ?? 0,    'bi-check-circle',         'ig-ind-verde'],
        ['inactivo',  'Inactivos',             $stats['inactivos'] ?? 0,  'bi-slash-circle',         'ig-ind-gris'],
        ['pendiente', 'Sin verificar correo',  $stats['pendientes'] ?? 0, 'bi-envelope-exclamation', 'ig-ind-ambar'],
    ];
    ?>
    <?php foreach ($tarjetas as [$valor, $texto, $n, $icono, $color]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= site_url('panel/usuarios' . ($valor ? '?estado=' . $valor : '')) ?>"
               class="ig-indicador <?= $color ?> <?= $filtros['estado'] === $valor && $filtros['q'] === '' && $filtros['rol'] === '' && $filtros['cargo'] === '' ? 'activo' : '' ?>">
                <i class="bi <?= $icono ?>"></i>
                <div><span class="ig-indicador-num"><?= (int) $n ?></span><span class="ig-indicador-txt"><?= $texto ?></span></div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<?php if (($stats['invitados'] ?? 0) > 0 && tiene_permiso('usuarios.gestionar') && $filtros['rol'] !== '4'): ?>
    <div class="alert ig-alerta-invitados d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span><i class="bi bi-hourglass-split me-1"></i>
            Hay <strong><?= (int) $stats['invitados'] ?></strong> usuario(s) con rol <strong>Invitado</strong> esperando que se les asigne un rol.</span>
        <a href="<?= site_url('panel/usuarios?rol=4&estado=activo') ?>" class="btn btn-sm btn-dark rounded-pill px-3">Ver invitados</a>
    </div>
<?php endif ?>

<!-- Filtros -->
<form method="get" action="<?= site_url('panel/usuarios') ?>" class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="q" class="form-label small mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" id="q" name="q" value="<?= esc($filtros['q']) ?>" placeholder="Nombre, CI, correo o celular">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label for="rol" class="form-label small mb-1">Rol</label>
                <select class="form-select" id="rol" name="rol">
                    <option value="">Todos</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int) $r['idrol'] ?>" <?= $filtros['rol'] === (string) $r['idrol'] ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="cargo" class="form-label small mb-1">Cargo</label>
                <select class="form-select" id="cargo" name="cargo">
                    <option value="">Todos</option>
                    <option value="sin" <?= $filtros['cargo'] === 'sin' ? 'selected' : '' ?>>Sin cargo</option>
                    <?php foreach ($cargos as $c): ?>
                        <option value="<?= (int) $c['idcargo'] ?>" <?= $filtros['cargo'] === (string) $c['idcargo'] ? 'selected' : '' ?>><?= esc($c['nombre']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="estado" class="form-label small mb-1">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>Activos</option>
                    <option value="inactivo" <?= $filtros['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivos</option>
                    <option value="pendiente" <?= $filtros['estado'] === 'pendiente' ? 'selected' : '' ?>>Sin verificar</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-ig flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <?php if ($filtros['q'] !== '' || $filtros['rol'] !== '' || $filtros['cargo'] !== '' || $filtros['estado'] !== ''): ?>
                    <a href="<?= site_url('panel/usuarios') ?>" class="btn btn-light" title="Limpiar filtros" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                <?php endif ?>
            </div>
        </div>
    </div>
</form>

<!-- Listado -->
<div class="card border-0 shadow-sm">
    <?php if ($usuarios === []): ?>
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-2"></i>No se encontraron usuarios con esos criterios.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0 ig-tabla">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col" class="d-none d-lg-table-cell">Cargo</th>
                        <th scope="col" class="d-none d-xl-table-cell">CI</th>
                        <th scope="col" class="d-none d-xl-table-cell">Celular</th>
                        <th scope="col">Rol</th>
                        <th scope="col" class="d-none d-md-table-cell">Estado</th>
                        <th scope="col" class="d-none d-xl-table-cell">Último acceso</th>
                        <th scope="col" class="text-end"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <?php [$estTxt, $estCls, $estIco] = ig_estado_usuario($u); ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('panel/usuarios/' . (int) $u['idusuario']) ?>" class="d-flex align-items-center gap-2 text-reset">
                                    <span class="ig-avatar ig-avatar-<?= (int) $u['idrol'] % 5 ?>"><?= esc(ig_iniciales($u)) ?></span>
                                    <span class="lh-sm">
                                        <span class="d-block fw-semibold"><?= esc($u['nombres'] . ' ' . $u['apellidos']) ?>
                                            <?php if ((int) $u['idusuario'] === (int) session('idusuario')): ?><span class="badge text-bg-light">Usted</span><?php endif ?></span>
                                        <span class="d-block small text-muted text-break"><?= esc($u['email']) ?></span>
                                        <span class="d-lg-none small text-ig-osc"><?= esc($u['cargo'] ?? '') ?></span>
                                        <span class="d-md-none small <?= $estCls ?>-txt"><i class="bi <?= $estIco ?> me-1"></i><?= $estTxt ?></span>
                                    </span>
                                </a>
                            </td>
                            <td class="d-none d-lg-table-cell small"><?= $u['cargo'] ? esc($u['cargo']) : '<span class="text-muted">Sin cargo</span>' ?></td>
                            <td class="d-none d-xl-table-cell"><?= esc($u['ci']) ?></td>
                            <td class="d-none d-xl-table-cell"><?= esc((string) $u['celular']) ?></td>
                            <td><span class="badge rounded-pill ig-badge-rol <?= (int) $u['idrol'] === 4 ? 'ig-badge-invitado' : '' ?>"><?= esc($u['rol']) ?></span></td>
                            <td class="d-none d-md-table-cell"><span class="ig-estado <?= $estCls ?>"><i class="bi <?= $estIco ?> me-1"></i><?= $estTxt ?></span></td>
                            <td class="d-none d-xl-table-cell small text-muted"><?= ig_fecha($u['ultimo_acceso'], true) ?></td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle ig-btn-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <?= $this->setData(['u' => $u, 'compacto' => true])->include('panel/usuarios/_acciones') ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 p-3">
            <small class="text-muted">
                Mostrando <?= count($usuarios) ?> de <?= $pager->getTotal() ?> usuario(s)
            </small>
            <?= $pager->links('default', 'bootstrap') ?>
        </div>
    <?php endif ?>
</div>

<?= $this->include('panel/usuarios/_modales') ?>
<?= $this->endSection() ?>
