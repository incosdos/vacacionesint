<?= $this->extend('layouts/panel') ?>
<?php $this->setVar('migas', ['Panel' => 'panel', 'Cargos' => null]); ?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Cargos</h1>
        <p class="text-muted mb-0">Puestos de trabajo que se asignan a cada usuario.</p>
    </div>
    <a href="<?= site_url('panel/cargos/nuevo') ?>" class="btn btn-ig px-3"><i class="bi bi-plus-lg me-1"></i> Nuevo cargo</a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['bi-briefcase', $totales['cargos'], 'Cargos registrados', ''],
        ['bi-check-circle', $totales['activos'], 'Cargos activos', 'ig-ind-verde'],
        ['bi-people', $totales['usuarios'], 'Usuarios con cargo', ''],
        ['bi-person-dash', $totales['sinCargo'], 'Usuarios sin cargo', 'ig-ind-ambar'],
    ] as [$ico, $num, $txt, $cls]): ?>
        <div class="col-6 col-lg-3">
            <?php if ($ico === 'bi-person-dash'): ?>
                <a href="<?= site_url('panel/usuarios?cargo=sin') ?>" class="ig-indicador <?= $cls ?>">
            <?php else: ?>
                <div class="ig-indicador <?= $cls ?>">
            <?php endif ?>
                <i class="bi <?= $ico ?>"></i>
                <div><span class="ig-indicador-num"><?= (int) $num ?></span><span class="ig-indicador-txt"><?= $txt ?></span></div>
            <?= $ico === 'bi-person-dash' ? '</a>' : '</div>' ?>
        </div>
    <?php endforeach ?>
</div>

<form method="get" action="<?= site_url('panel/cargos') ?>" class="mb-3" role="search">
    <div class="input-group" style="max-width: 420px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="search" class="form-control" name="q" value="<?= esc($q) ?>" placeholder="Buscar cargo" aria-label="Buscar cargo">
        <?php if ($q !== ''): ?><a href="<?= site_url('panel/cargos') ?>" class="btn btn-light border" aria-label="Limpiar"><i class="bi bi-x-lg"></i></a><?php endif ?>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <?php if ($cargos === []): ?>
        <div class="card-body text-center text-muted py-5"><i class="bi bi-briefcase fs-1 d-block mb-2"></i>No se encontraron cargos.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0 ig-tabla">
                <thead>
                    <tr>
                        <th scope="col">Cargo</th>
                        <th scope="col" class="d-none d-md-table-cell">Descripción</th>
                        <th scope="col" class="text-center">Usuarios</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cargos as $c): ?>
                        <?php $id = (int) $c['idcargo']; $n = (int) $c['total_usuarios']; $activo = (int) $c['estado'] === 1; ?>
                        <tr class="<?= $activo ? '' : 'ig-fila-inactiva' ?>">
                            <td>
                                <a href="<?= site_url("panel/cargos/{$id}/editar") ?>" class="fw-semibold text-reset d-flex align-items-center gap-2">
                                    <span class="ig-modulo-icono ig-icono-sm"><i class="bi bi-briefcase"></i></span><?= esc($c['nombre']) ?>
                                </a>
                            </td>
                            <td class="d-none d-md-table-cell small text-muted"><?= esc($c['descripcion'] ?? '—') ?></td>
                            <td class="text-center">
                                <?php if ($n > 0): ?>
                                    <a href="<?= site_url('panel/usuarios?cargo=' . $id) ?>" class="badge rounded-pill ig-badge-rol text-decoration-none"><?= $n ?></a>
                                <?php else: ?><span class="text-muted">0</span><?php endif ?>
                            </td>
                            <td>
                                <span class="ig-estado <?= $activo ? 'ig-estado-activo' : 'ig-estado-inactivo' ?>">
                                    <i class="bi <?= $activo ? 'bi-check-circle' : 'bi-slash-circle' ?> me-1"></i><?= $activo ? 'Activo' : 'Inactivo' ?></span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle ig-btn-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones">
                                        <i class="bi bi-three-dots-vertical"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <a class="dropdown-item" href="<?= site_url("panel/cargos/{$id}/editar") ?>"><i class="bi bi-pencil-square me-2"></i>Editar</a>
                                        <button type="button" class="dropdown-item <?= $activo ? 'text-warning-emphasis' : 'text-success' ?>" data-bs-toggle="modal" data-bs-target="#modalConfirmar"
                                                data-url="<?= site_url("panel/cargos/{$id}/estado") ?>" data-titulo="<?= $activo ? 'Desactivar cargo' : 'Activar cargo' ?>"
                                                data-texto="<?= esc($activo ? "«{$c['nombre']}» ya no aparecerá al asignar cargos. Los {$n} usuario(s) que lo tienen lo conservan." : "«{$c['nombre']}» volverá a estar disponible para asignar.", 'attr') ?>"
                                                data-boton="<?= $activo ? 'Desactivar' : 'Activar' ?>" data-estilo="<?= $activo ? 'btn-warning' : 'btn-success' ?>">
                                            <i class="bi <?= $activo ? 'bi-toggle-off' : 'bi-toggle-on' ?> me-2"></i><?= $activo ? 'Desactivar' : 'Activar' ?></button>
                                        <hr class="dropdown-divider">
                                        <?php if ($n > 0): ?>
                                            <span class="dropdown-item-text small text-muted"><i class="bi bi-trash me-2"></i>No se puede eliminar: tiene usuarios</span>
                                        <?php else: ?>
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmar"
                                                    data-url="<?= site_url("panel/cargos/{$id}/eliminar") ?>" data-titulo="Eliminar cargo"
                                                    data-texto="<?= esc("¿Eliminar definitivamente el cargo «{$c['nombre']}»?", 'attr') ?>" data-boton="Eliminar" data-estilo="btn-danger">
                                                <i class="bi bi-trash me-2"></i>Eliminar</button>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>

<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-labelledby="modalConfirmarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <?= form_open('', ['id' => 'formConfirmar']) ?>
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="modalConfirmarTitulo"></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-0" id="modalConfirmarTexto"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn rounded-pill px-3" id="modalConfirmarBoton"></button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('modalConfirmar').addEventListener('show.bs.modal', function (e) {
    const d = e.relatedTarget.dataset;
    document.getElementById('formConfirmar').action = d.url;
    document.getElementById('modalConfirmarTitulo').textContent = d.titulo;
    document.getElementById('modalConfirmarTexto').textContent = d.texto;
    const b = document.getElementById('modalConfirmarBoton');
    b.textContent = d.boton;
    b.className = 'btn rounded-pill px-3 ' + d.estilo;
});
</script>
<?= $this->endSection() ?>
