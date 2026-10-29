<?= $this->extend('layouts/panel') ?>

<?= $this->section('contenido') ?>
<?= $this->include('panel/roles/_encabezado') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Roles y permisos</h1>
        <p class="text-muted mb-0">Defina qué puede hacer cada tipo de usuario en el sistema.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= site_url('panel/roles/matriz') ?>" class="btn btn-outline-dark rounded-pill px-3">
            <i class="bi bi-grid-3x3-gap me-1"></i> Matriz de permisos
        </a>
        <a href="<?= site_url('panel/roles/nuevo') ?>" class="btn btn-ig px-3">
            <i class="bi bi-plus-lg me-1"></i> Nuevo rol
        </a>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($roles as $r): ?>
        <?php
        $id        = (int) $r['idrol'];
        $protegido = \App\Models\RolModel::esProtegido($id);
        $usuarios  = (int) $r['total_usuarios'];
        $nPermisos = (int) $r['total_permisos'];
        $porc      = $totalPermisos > 0 ? round($nPermisos * 100 / $totalPermisos) : 0;
        ?>
        <div class="col-md-6 col-xl-4">
            <div class="ig-rol h-100">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="ig-modulo-icono"><i class="bi <?= $id === 1 ? 'bi-shield-fill-check' : 'bi-person-badge' ?>"></i></span>
                        <div>
                            <h2 class="h5 fw-semibold mb-0"><?= esc($r['nombre']) ?></h2>
                            <?php if ($protegido): ?>
                                <span class="badge rounded-pill ig-badge-base"><i class="bi bi-lock-fill me-1"></i>Rol base</span>
                            <?php else: ?>
                                <span class="badge rounded-pill text-bg-light">Personalizado</span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>

                <p class="small text-muted mb-3"><?= esc($r['descripcion'] ?? 'Sin descripción.') ?></p>

                <div class="d-flex justify-content-between small mb-1">
                    <span><i class="bi bi-key me-1"></i><?= $nPermisos ?> de <?= $totalPermisos ?> permisos</span>
                    <span><i class="bi bi-people me-1"></i><?= $usuarios ?> usuario<?= $usuarios === 1 ? '' : 's' ?></span>
                </div>
                <div class="progress ig-progress mb-3" role="progressbar" aria-label="Permisos asignados" aria-valuenow="<?= $porc ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: <?= $porc ?>%"></div>
                </div>

                <div class="d-flex gap-2 mt-auto">
                    <a href="<?= site_url("panel/roles/{$id}/editar") ?>" class="btn btn-sm btn-ig px-3 flex-grow-1">
                        <i class="bi bi-pencil-square me-1"></i> Editar y permisos
                    </a>
                    <?php if ($protegido || $usuarios > 0): ?>
                        <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                              data-bs-title="<?= $protegido ? 'Los roles base no se pueden eliminar' : 'Tiene usuarios asignados' ?>">
                            <button class="btn btn-sm btn-outline-danger rounded-pill" disabled aria-label="Eliminar"><i class="bi bi-trash"></i></button>
                        </span>
                    <?php else: ?>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" aria-label="Eliminar"
                                data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                data-url="<?= site_url("panel/roles/{$id}/eliminar") ?>" data-nombre="<?= esc($r['nombre'], 'attr') ?>">
                            <i class="bi bi-trash"></i>
                        </button>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<!-- Confirmación de eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <?= form_open('', ['id' => 'formEliminar']) ?>
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="modalEliminarTitulo"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Eliminar rol</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-0">
                    ¿Seguro que desea eliminar el rol <strong id="nombreEliminar"></strong>? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-3"><i class="bi bi-trash me-1"></i>Eliminar</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function (e) {
    document.getElementById('formEliminar').action = e.relatedTarget.dataset.url;
    document.getElementById('nombreEliminar').textContent = e.relatedTarget.dataset.nombre;
});
</script>
<?= $this->endSection() ?>
