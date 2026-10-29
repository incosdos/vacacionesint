<?= $this->extend('layouts/panel') ?>

<?php $this->setVar('miga', 'Matriz de permisos'); ?>

<?= $this->section('contenido') ?>
<?= $this->include('panel/roles/_encabezado') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Matriz de permisos</h1>
        <p class="text-muted mb-0">Vista general de todos los roles. Marque o desmarque y guarde los cambios.</p>
    </div>
</div>

<?= form_open('panel/roles/matriz') ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive ig-matriz-scroll">
        <table class="table table-hover align-middle mb-0 ig-matriz">
            <thead>
                <tr>
                    <th scope="col" class="ig-matriz-fija">Permiso</th>
                    <?php foreach ($roles as $r): ?>
                        <th scope="col" class="text-center">
                            <?= esc($r['nombre']) ?>
                            <?php if ((int) $r['idrol'] === 1): ?><i class="bi bi-lock-fill text-white-50 small" title="Siempre tiene todos los permisos"></i><?php endif ?>
                        </th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $nombreGrupo => $g): ?>
                    <tr class="ig-matriz-grupo">
                        <th scope="rowgroup" colspan="<?= count($roles) + 1 ?>"><i class="bi <?= esc($g['icono']) ?> me-1"></i><?= esc($nombreGrupo) ?></th>
                    </tr>
                    <?php foreach ($g['permisos'] as $p): ?>
                        <?php $pid = (int) $p['idpermiso']; ?>
                        <tr>
                            <th scope="row" class="ig-matriz-fija fw-normal">
                                <?= esc($p['descripcion']) ?>
                                <code class="d-block small text-muted"><?= esc($p['clave']) ?></code>
                            </th>
                            <?php foreach ($roles as $r): ?>
                                <?php $rid = (int) $r['idrol']; $admin = $rid === 1; ?>
                                <td class="text-center">
                                    <input class="form-check-input ig-check-lg" type="checkbox" name="permisos[<?= $rid ?>][]" value="<?= $pid ?>"
                                           aria-label="<?= esc($r['nombre'] . ': ' . $p['descripcion'], 'attr') ?>"
                                           <?= $admin || in_array($pid, $mapa[$rid] ?? [], true) ? 'checked' : '' ?> <?= $admin ? 'disabled' : '' ?>>
                                </td>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 p-3 d-flex flex-wrap justify-content-end gap-2">
        <a href="<?= site_url('panel/roles') ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
        <button type="submit" class="btn btn-ig px-4"><i class="bi bi-check2-circle me-1"></i>Guardar cambios</button>
    </div>
</div>
<?= form_close() ?>
<?= $this->endSection() ?>
