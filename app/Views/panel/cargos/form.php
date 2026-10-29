<?= $this->extend('layouts/panel') ?>
<?php
$id  = $c['idcargo'] !== null ? (int) $c['idcargo'] : null;
$err = session('errores') ?? [];
$this->setVar('migas', ['Panel' => 'panel', 'Cargos' => 'panel/cargos', ($id === null ? 'Nuevo cargo' : 'Editar') => null]);
?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>
<h1 class="h3 fw-bold mb-4"><?= $id === null ? 'Nuevo cargo' : 'Editar cargo: ' . esc($c['nombre']) ?></h1>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <?= form_open($id === null ? 'panel/cargos' : "panel/cargos/{$id}", ['id' => 'formCargo', 'novalidate' => true]) ?>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del cargo</label>
                        <input type="text" class="form-control <?= isset($err['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre"
                               value="<?= esc(old('nombre', $c['nombre'])) ?>" maxlength="80" minlength="3" required autofocus placeholder="Ej.: Diseñador Gráfico">
                        <div class="invalid-feedback"><?= esc($err['nombre'] ?? 'Ingrese un nombre de al menos 3 caracteres.') ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción <span class="text-muted small">(opcional)</span></label>
                        <textarea class="form-control <?= isset($err['descripcion']) ? 'is-invalid' : '' ?>" id="descripcion" name="descripcion" rows="3" maxlength="255"
                                  placeholder="Funciones principales del cargo"><?= esc(old('descripcion', $c['descripcion'] ?? '')) ?></textarea>
                        <div class="invalid-feedback"><?= esc($err['descripcion'] ?? '') ?></div>
                    </div>
                    <div class="mb-4">
                        <input type="hidden" name="estado" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="estado" name="estado" value="1" <?= (string) old('estado', (string) $c['estado']) === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="estado">Cargo activo <span class="text-muted small">(disponible para asignar)</span></label>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <a href="<?= site_url('panel/cargos') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-ig px-4"><i class="bi bi-check2-circle me-1"></i>Guardar</button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>

    <?php if ($id !== null): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h6 fw-semibold mb-3"><i class="bi bi-people me-1 text-ig-osc"></i>Usuarios con este cargo
                        <span class="badge rounded-pill text-bg-light ms-1"><?= count($usuarios) ?></span></h2>
                    <?php if ($usuarios === []): ?>
                        <p class="small text-muted mb-0">Ningún usuario tiene este cargo.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($usuarios as $u): ?>
                                <li class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom">
                                    <a href="<?= site_url('panel/usuarios/' . (int) $u['idusuario']) ?>" class="text-reset">
                                        <span class="fw-semibold d-block small"><?= esc($u['nombres'] . ' ' . $u['apellidos']) ?></span>
                                        <span class="small text-muted"><?= esc($u['email']) ?></span>
                                    </a>
                                    <span class="badge rounded-pill ig-badge-rol"><?= esc($u['rol']) ?></span>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('formCargo').addEventListener('submit', function (e) {
    const f = this;
    f.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    if (!f.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
    f.classList.add('was-validated');
});
</script>
<?= $this->endSection() ?>
