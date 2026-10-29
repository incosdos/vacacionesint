<?= $this->extend('layouts/panel') ?>
<?php $this->setVar('migas', ['Panel' => 'panel', 'Solicitudes del personal' => null]); ?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>

<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Solicitudes del personal</h1>
    <p class="text-muted mb-0"><?= $puedeAutorizar ? 'Revise, autorice o rechace las solicitudes de vacaciones.' : 'Consulta de las vacaciones del personal.' ?></p>
</div>

<ul class="nav nav-pills ig-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?= $vista === 'pendientes' ? 'active' : '' ?>" href="<?= site_url('panel/solicitudes') ?>">
            <i class="bi bi-hourglass-split me-1"></i>Pendientes <span class="badge rounded-pill ms-1 ig-badge-cont"><?= (int) $totalPend ?></span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $vista === 'historial' ? 'active' : '' ?>" href="<?= site_url('panel/solicitudes?vista=historial') ?>"><i class="bi bi-clock-history me-1"></i>Historial</a>
    </li>
</ul>

<?php if ($vista === 'pendientes'): ?>

    <?php if ($pendientes === []): ?>
        <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">
            <i class="bi bi-check2-all fs-1 d-block mb-2 text-ig"></i>No hay solicitudes pendientes. ¡Todo al día!
        </div></div>
    <?php endif ?>

    <div class="row g-4">
    <?php foreach ($pendientes as $p): ?>
        <?php $s = $p['saldo']; ?>
        <div class="col-xl-6">
            <div class="ig-pendiente h-100">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="ig-avatar ig-avatar-<?= (int) $p['idrol'] % 5 ?>"><?= esc(mb_strtoupper(mb_substr($p['solicitante'], 0, 1))) ?></span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?= esc($p['solicitante']) ?>
                            <?php if ($p['propia']): ?><span class="badge text-bg-light">Su solicitud</span><?php endif ?></div>
                        <div class="small text-muted"><?= esc($p['rol']) ?> · solicitada el <?= esc(vac_fecha($p['fecha_solicitud'], 'd MMM y')) ?></div>
                    </div>
                    <div class="text-end">
                        <div class="ig-pendiente-dias"><?= rtrim(rtrim(number_format((float) $p['dias_habiles'], 1, ',', ''), '0'), ',') ?></div>
                        <div class="small text-muted">días hábiles</div>
                    </div>
                </div>

                <div class="ig-pendiente-periodo mb-3"><i class="bi bi-calendar-range me-2"></i><?= esc(vac_periodo($p['fecha_inicio'], $p['fecha_fin'])) ?></div>

                <?php if ($p['motivo']): ?><p class="small mb-2"><span class="text-muted">Motivo:</span> <?= esc($p['motivo']) ?></p><?php endif ?>

                <p class="small mb-2">
                    <span class="text-muted">Saldo gestión <?= (int) $s['gestion'] ?>:</span>
                    <?php if ($s['disponibles'] === null): ?>
                        <span class="text-warning-emphasis">sin fecha de ingreso registrada</span>
                    <?php else: ?>
                        <?php $queda = $s['disponibles'] - (float) $p['dias_habiles']; ?>
                        <?= vac_dias($s['disponibles']) ?> disponibles →
                        <strong class="<?= $queda < 0 ? 'text-danger' : 'text-ig-osc' ?>">le quedarían <?= vac_dias(max(0, $queda)) ?></strong>
                        <?php if ($queda < 0): ?><span class="badge text-bg-danger ms-1">Excede el saldo</span><?php endif ?>
                    <?php endif ?>
                </p>

                <?php if ($p['feriados'] !== []): ?>
                    <p class="small mb-2"><span class="text-muted">Feriados en el periodo:</span>
                        <?= esc(implode(', ', array_map(static fn ($f, $n) => $n . ' (' . vac_fecha($f, 'd MMM') . ')', array_keys($p['feriados']), $p['feriados']))) ?></p>
                <?php endif ?>

                <div class="small mb-3">
                    <span class="text-muted">También ausentes en esas fechas:</span>
                    <?php if ($p['ausentes'] === []): ?>
                        <span class="text-success">nadie</span>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <?php foreach ($p['ausentes'] as $a): ?>
                                <span class="ig-chip <?= $a['estado'] === 'pendiente' ? 'ig-chip-pend' : '' ?>" title="<?= esc(vac_periodo($a['fecha_inicio'], $a['fecha_fin']), 'attr') ?>">
                                    <?= esc($a['solicitante']) ?> · <?= esc(vac_fecha($a['fecha_inicio'], 'd MMM')) ?>–<?= esc(vac_fecha($a['fecha_fin'], 'd MMM')) ?><?= $a['estado'] === 'pendiente' ? ' (pendiente)' : '' ?>
                                </span>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                </div>

                <?php if ($puedeAutorizar && ! $p['propia']): ?>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-ig flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalResolver"
                                data-accion="autorizar" data-url="<?= site_url("panel/solicitudes/{$p['idvacacion']}/resolver") ?>"
                                data-nombre="<?= esc($p['solicitante'], 'attr') ?>" data-periodo="<?= esc(vac_periodo($p['fecha_inicio'], $p['fecha_fin']), 'attr') ?>">
                            <i class="bi bi-check-lg me-1"></i>Autorizar</button>
                        <button type="button" class="btn btn-outline-danger rounded-pill flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalResolver"
                                data-accion="rechazar" data-url="<?= site_url("panel/solicitudes/{$p['idvacacion']}/resolver") ?>"
                                data-nombre="<?= esc($p['solicitante'], 'attr') ?>" data-periodo="<?= esc(vac_periodo($p['fecha_inicio'], $p['fecha_fin']), 'attr') ?>">
                            <i class="bi bi-x-lg me-1"></i>Rechazar</button>
                    </div>
                <?php elseif ($p['propia']): ?>
                    <p class="small text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Su solicitud debe resolverla otro coordinador o el administrador.</p>
                <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
    </div>

    <?php if ($puedeAutorizar): ?>
    <div class="modal fade" id="modalResolver" tabindex="-1" aria-labelledby="modalResolverTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <?= form_open('', ['id' => 'formResolver', 'novalidate' => true]) ?>
                    <input type="hidden" name="accion" id="resolverAccion">
                    <div class="modal-header border-0">
                        <h2 class="modal-title h5" id="modalResolverTitulo"></h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <p class="mb-3"><strong id="resolverNombre"></strong><br><span class="text-muted" id="resolverPeriodo"></span></p>
                        <label for="observacion" class="form-label" id="resolverLabel"></label>
                        <textarea class="form-control" name="observacion" id="observacion" rows="3" maxlength="255"></textarea>
                        <div class="invalid-feedback">Indique el motivo del rechazo (al menos 5 caracteres).</div>
                        <div class="form-text">La persona recibirá un correo con la decisión.</div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn rounded-pill px-3" id="resolverBoton"></button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <?php endif ?>

<?php else: ?>

    <form method="get" action="<?= site_url('panel/solicitudes') ?>" class="card border-0 shadow-sm mb-4">
        <input type="hidden" name="vista" value="historial">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label for="q" class="form-label small mb-1">Colaborador</label>
                    <input type="search" class="form-control" id="q" name="q" value="<?= esc($filtros['q']) ?>" placeholder="Nombre, CI o correo">
                </div>
                <div class="col-6 col-md-2">
                    <label for="estado" class="form-label small mb-1">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos</option>
                        <?php foreach (['pendiente' => 'Pendientes', 'autorizado' => 'Autorizadas', 'rechazado' => 'Rechazadas', 'cancelado' => 'Canceladas'] as $k => $t): ?>
                            <option value="<?= $k ?>" <?= $filtros['estado'] === $k ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="desde" class="form-label small mb-1">Desde</label>
                    <input type="date" class="form-control" id="desde" name="desde" value="<?= esc($filtros['desde']) ?>">
                </div>
                <div class="col-6 col-md-2">
                    <label for="hasta" class="form-label small mb-1">Hasta</label>
                    <input type="date" class="form-control" id="hasta" name="hasta" value="<?= esc($filtros['hasta']) ?>">
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button class="btn btn-ig flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <?php if (array_filter($filtros)): ?>
                        <a href="<?= site_url('panel/solicitudes?vista=historial') ?>" class="btn btn-light" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <?php if ($solicitudes === []): ?>
            <div class="card-body text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-2"></i>No hay solicitudes con esos criterios.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0 ig-tabla">
                    <thead>
                        <tr>
                            <th scope="col">Colaborador</th>
                            <th scope="col">Periodo</th>
                            <th scope="col" class="text-center">Días</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="d-none d-lg-table-cell">Revisada por</th>
                            <th scope="col" class="d-none d-xl-table-cell">Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <?php [$t, $c, $i] = vac_estado($s['estado']); ?>
                            <tr>
                                <td><span class="fw-semibold d-block"><?= esc($s['solicitante']) ?></span><span class="small text-muted">CI <?= esc($s['ci']) ?></span></td>
                                <td class="small"><?= esc(vac_periodo($s['fecha_inicio'], $s['fecha_fin'])) ?></td>
                                <td class="text-center"><?= rtrim(rtrim(number_format((float) $s['dias_habiles'], 1, ',', ''), '0'), ',') ?></td>
                                <td><span class="ig-vac-estado <?= $c ?>"><i class="bi <?= $i ?> me-1"></i><?= $t ?></span></td>
                                <td class="d-none d-lg-table-cell small"><?= esc($s['autorizador'] ?? '—') ?>
                                    <?php if ($s['fecha_autorizacion']): ?><span class="d-block text-muted"><?= esc(vac_fecha($s['fecha_autorizacion'], 'd MMM y')) ?></span><?php endif ?></td>
                                <td class="d-none d-xl-table-cell small text-muted"><?= esc($s['observacion_coordinador'] ?? '') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 p-3">
                <small class="text-muted">Mostrando <?= count($solicitudes) ?> de <?= $pager->getTotal() ?> solicitud(es)</small>
                <?= $pager->links('default', 'bootstrap') ?>
            </div>
        <?php endif ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const modal = document.getElementById('modalResolver');
    if (!modal) return;
    const obs = document.getElementById('observacion'), form = document.getElementById('formResolver');
    modal.addEventListener('show.bs.modal', function (e) {
        const d = e.relatedTarget.dataset, rechazar = d.accion === 'rechazar';
        form.action = d.url;
        document.getElementById('resolverAccion').value = d.accion;
        document.getElementById('resolverNombre').textContent = d.nombre;
        document.getElementById('resolverPeriodo').textContent = d.periodo;
        document.getElementById('modalResolverTitulo').textContent = rechazar ? 'Rechazar solicitud' : 'Autorizar vacaciones';
        document.getElementById('resolverLabel').innerHTML = rechazar ? 'Motivo del rechazo <span class="text-danger">*</span>' : 'Observación <span class="text-muted small">(opcional)</span>';
        const b = document.getElementById('resolverBoton');
        b.textContent = rechazar ? 'Rechazar' : 'Autorizar';
        b.className = 'btn rounded-pill px-3 ' + (rechazar ? 'btn-danger' : 'btn-ig');
        obs.value = ''; obs.classList.remove('is-invalid'); obs.required = rechazar;
    });
    form.addEventListener('submit', function (e) {
        if (obs.required && obs.value.trim().length < 5) { e.preventDefault(); obs.classList.add('is-invalid'); obs.focus(); }
    });
})();
</script>
<?= $this->endSection() ?>
