<?= $this->extend('layouts/panel') ?>

<?php
$id       = $rol['idrol'] !== null ? (int) $rol['idrol'] : null;
$esAdmin  = $id === \App\Models\RolModel::ADMINISTRADOR;
$err      = session('errores') ?? [];
$marcados = old('permisos') !== null ? array_map('intval', (array) old('permisos')) : $seleccionados;
$this->setVar('miga', $id === null ? 'Nuevo rol' : 'Editar «' . $rol['nombre'] . '»');
?>

<?= $this->section('contenido') ?>
<?= $this->include('panel/roles/_encabezado') ?>

<h1 class="h3 fw-bold mb-4"><?= $id === null ? 'Nuevo rol' : 'Editar rol: ' . esc($rol['nombre']) ?></h1>

<?= form_open($id === null ? 'panel/roles' : "panel/roles/{$id}", ['id' => 'formRol', 'novalidate' => true]) ?>
<div class="row g-4">
    <!-- Datos del rol -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm ig-sticky">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-person-badge me-1 text-ig-osc"></i>Datos del rol</h2>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control <?= isset($err['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre"
                           value="<?= esc(old('nombre', $rol['nombre'])) ?>" maxlength="50" required <?= $esAdmin ? 'readonly' : '' ?>>
                    <div class="invalid-feedback"><?= esc($err['nombre'] ?? 'Ingrese un nombre de al menos 3 caracteres.') ?></div>
                    <?php if ($esAdmin): ?><div class="form-text">El nombre del rol Administrador no se puede cambiar.</div><?php endif ?>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control <?= isset($err['descripcion']) ? 'is-invalid' : '' ?>" id="descripcion" name="descripcion"
                              rows="3" maxlength="255"><?= esc(old('descripcion', $rol['descripcion'] ?? '')) ?></textarea>
                    <div class="invalid-feedback"><?= esc($err['descripcion'] ?? '') ?></div>
                </div>

                <?php if ($id !== null): ?>
                    <p class="small text-muted mb-3"><i class="bi bi-people me-1"></i><?= (int) $totalUsuarios ?> usuario(s) con este rol.
                        Los cambios se aplican de inmediato.</p>
                <?php endif ?>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-ig py-2"><i class="bi bi-check2-circle me-1"></i>Guardar</button>
                    <a href="<?= site_url('panel/roles') ?>" class="btn btn-light rounded-pill">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos -->
    <div class="col-lg-8">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 fw-semibold mb-0"><i class="bi bi-key me-1 text-ig-osc"></i>Permisos
                <span class="badge rounded-pill text-bg-light ms-1" id="contador"></span></h2>
            <?php if (! $esAdmin): ?>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" data-marcar="todos">Marcar todos</button>
                    <button type="button" class="btn btn-outline-secondary" data-marcar="ninguno">Quitar todos</button>
                </div>
            <?php endif ?>
        </div>

        <div class="alert alert-warning py-2 small d-none" id="avisoPanel"><i class="bi bi-exclamation-triangle me-1"></i>
            Sin el permiso <strong>«Acceder al panel principal»</strong> los usuarios de este rol no podrán entrar al sistema.</div>

        <?php if ($esAdmin): ?>
            <div class="alert alert-info py-2 small"><i class="bi bi-lock me-1"></i>
                El Administrador siempre tiene todos los permisos, para evitar que el sistema quede sin nadie que lo administre.</div>
        <?php endif ?>

        <div class="row g-3">
            <?php foreach ($grupos as $nombreGrupo => $g): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 ig-grupo">
                        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold"><i class="bi <?= esc($g['icono']) ?> me-1 text-ig-osc"></i><?= esc($nombreGrupo) ?></span>
                            <?php if (! $esAdmin): ?>
                                <div class="form-check form-switch m-0" title="Marcar todo el grupo">
                                    <input class="form-check-input ig-grupo-todos" type="checkbox" role="switch" aria-label="Marcar todo <?= esc($nombreGrupo, 'attr') ?>">
                                </div>
                            <?php endif ?>
                        </div>
                        <div class="card-body pt-1">
                            <?php foreach ($g['permisos'] as $p): ?>
                                <?php $pid = (int) $p['idpermiso']; ?>
                                <div class="form-check ig-permiso">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="<?= $pid ?>" id="p<?= $pid ?>"
                                        <?= $esAdmin || in_array($pid, $marcados, true) ? 'checked' : '' ?> <?= $esAdmin ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="p<?= $pid ?>">
                                        <?= esc($p['descripcion']) ?>
                                        <code class="d-block small text-muted"><?= esc($p['clave']) ?></code>
                                    </label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?= form_close() ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const form = document.getElementById('formRol');
    const checks = () => form.querySelectorAll('input[name="permisos[]"]');
    const actualizar = () => {
        const todos = checks();
        const marcados = [...todos].filter(c => c.checked).length;
        document.getElementById('contador').textContent = marcados + ' de ' + todos.length;
        const panel = [...todos].find(c => c.closest('.ig-permiso').querySelector('code').textContent.trim() === 'panel.ver');
        document.getElementById('avisoPanel').classList.toggle('d-none', !panel || panel.checked);
        form.querySelectorAll('.ig-grupo').forEach(function (g) {
            const sw = g.querySelector('.ig-grupo-todos');
            if (!sw) return;
            const cs = [...g.querySelectorAll('input[name="permisos[]"]')];
            sw.checked = cs.every(c => c.checked);
            sw.indeterminate = !sw.checked && cs.some(c => c.checked);
        });
    };
    form.addEventListener('change', function (e) {
        if (e.target.classList.contains('ig-grupo-todos')) {
            e.target.closest('.ig-grupo').querySelectorAll('input[name="permisos[]"]').forEach(c => c.checked = e.target.checked);
        }
        actualizar();
    });
    document.querySelectorAll('[data-marcar]').forEach(function (b) {
        b.addEventListener('click', function () {
            checks().forEach(c => c.checked = b.dataset.marcar === 'todos');
            actualizar();
        });
    });
    form.addEventListener('submit', function (e) {
        const nombre = document.getElementById('nombre');
        if (!nombre.readOnly && nombre.value.trim().length < 3) {
            e.preventDefault();
            nombre.classList.add('is-invalid');
            nombre.focus();
        }
    });
    actualizar();
})();
</script>
<?= $this->endSection() ?>
