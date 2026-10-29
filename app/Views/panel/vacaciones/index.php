<?= $this->extend('layouts/panel') ?>
<?php $this->setVar('migas', ['Panel' => 'panel', 'Mis vacaciones' => null]); ?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Mis vacaciones</h1>
        <p class="text-muted mb-0">Gestión <?= (int) $saldo['gestion'] ?>
            <?php if ($antiguedad !== null): ?>
                · Antigüedad: <?= $antiguedad['anios'] ?> año<?= $antiguedad['anios'] === 1 ? '' : 's' ?><?= $antiguedad['meses'] ? ' y ' . $antiguedad['meses'] . ' mes' . ($antiguedad['meses'] === 1 ? '' : 'es') : '' ?>
            <?php endif ?>
        </p>
    </div>
    <?php if (tiene_permiso('vacaciones.solicitar')): ?>
        <a href="<?= site_url('panel/vacaciones/solicitar') ?>" class="btn btn-ig px-3"><i class="bi bi-calendar-plus me-1"></i> Solicitar vacaciones</a>
    <?php endif ?>
</div>

<?= $this->include('panel/vacaciones/_saldo') ?>

<?php if ($saldo['asignados'] === null): ?>
    <div class="alert alert-light border small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>
        Su fecha de ingreso no está registrada, por eso no se muestra el saldo. Solicite a administración que la complete.</div>
<?php elseif ($saldo['asignados'] === 0): ?>
    <div class="alert alert-light border small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>
        Las vacaciones se adquieren al cumplir un año de antigüedad en la empresa.</div>
<?php endif ?>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <h2 class="h6 fw-semibold mb-3"><i class="bi bi-list-ul me-1 text-ig-osc"></i>Mis solicitudes</h2>
        <?php if ($solicitudes === []): ?>
            <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">
                <i class="bi bi-sun fs-1 d-block mb-2 text-ig"></i>Aún no registró solicitudes de vacaciones.
            </div></div>
        <?php endif ?>
        <?php foreach ($solicitudes as $s): ?>
            <?php
            [$estTxt, $estCls, $estIco] = vac_estado($s['estado']);
            $cancelable = $s['estado'] === 'pendiente' || ($s['estado'] === 'autorizado' && $s['fecha_inicio'] > date('Y-m-d'));
            ?>
            <div class="ig-solicitud <?= $estCls ?>-borde mb-3">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold"><?= esc(vac_periodo($s['fecha_inicio'], $s['fecha_fin'])) ?></div>
                        <div class="small text-muted"><?= vac_dias($s['dias_habiles']) ?> hábiles · solicitada el <?= esc(vac_fecha($s['fecha_solicitud'], 'd MMM y')) ?></div>
                    </div>
                    <span class="ig-vac-estado <?= $estCls ?>"><i class="bi <?= $estIco ?> me-1"></i><?= $estTxt ?></span>
                </div>
                <?php if ($s['motivo']): ?><p class="small mb-0 mt-2"><span class="text-muted">Motivo:</span> <?= esc($s['motivo']) ?></p><?php endif ?>
                <?php if ($s['autorizador']): ?>
                    <p class="small mb-0 mt-2"><span class="text-muted"><?= $s['estado'] === 'rechazado' ? 'Rechazada' : 'Revisada' ?> por:</span> <?= esc($s['autorizador']) ?>
                        <?php if ($s['observacion_coordinador']): ?> · «<?= esc($s['observacion_coordinador']) ?>»<?php endif ?></p>
                <?php endif ?>
                <?php if ($cancelable && tiene_permiso('vacaciones.solicitar')): ?>
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCancelar"
                                data-url="<?= site_url("panel/vacaciones/{$s['idvacacion']}/cancelar") ?>" data-periodo="<?= esc(vac_periodo($s['fecha_inicio'], $s['fecha_fin']), 'attr') ?>">
                            <i class="bi bi-x-lg me-1"></i>Cancelar solicitud
                        </button>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-calendar-event me-1 text-ig-osc"></i>Próximos feriados</h2>
                <ul class="list-unstyled mb-0 small">
                    <?php foreach ($proximos as $fecha => $nombre): ?>
                        <li class="d-flex justify-content-between gap-2 py-2 border-bottom">
                            <span><?= esc($nombre) ?></span><span class="text-muted text-nowrap"><?= esc(vac_fecha($fecha, 'EEE d MMM')) ?></span>
                        </li>
                    <?php endforeach ?>
                </ul>
                <p class="small text-muted mt-3 mb-0">Los fines de semana y feriados no se descuentan de sus días de vacación.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCancelar" tabindex="-1" aria-labelledby="modalCancelarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <?= form_open('', ['id' => 'formCancelar']) ?>
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="modalCancelarTitulo">Cancelar solicitud</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-0">¿Desea cancelar sus vacaciones del <strong id="cancelarPeriodo"></strong>? Los días volverán a su saldo.</div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-3">Sí, cancelar</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('modalCancelar').addEventListener('show.bs.modal', function (e) {
    document.getElementById('formCancelar').action = e.relatedTarget.dataset.url;
    document.getElementById('cancelarPeriodo').textContent = e.relatedTarget.dataset.periodo;
});
</script>
<?= $this->endSection() ?>
