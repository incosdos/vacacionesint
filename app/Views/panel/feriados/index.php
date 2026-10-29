<?= $this->extend('layouts/panel') ?>
<?php
$this->setVar('migas', ['Panel' => 'panel', 'Feriados' => null]);
$clsTipo = ['nacional' => 'ig-fer-nacional', 'departamental' => 'ig-fer-departamental', 'no_laborable' => 'ig-fer-nolaborable'];
$porMes = [];
foreach ($feriados as $f) { $porMes[(int) substr($f['fecha'], 5, 2)][] = $f; }
$hoy = date('Y-m-d');
?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Feriados y días no laborables</h1>
        <p class="text-muted mb-0">Estos días no se descuentan de las vacaciones del personal.</p>
    </div>
    <a href="<?= site_url('panel/feriados/nuevo?fecha=' . ($anio === (int) date('Y') ? '' : $anio . '-01-01')) ?>" class="btn btn-ig px-3"><i class="bi bi-plus-lg me-1"></i> Agregar día</a>
</div>

<!-- Años -->
<ul class="nav nav-pills ig-tabs mb-4 flex-wrap">
    <?php foreach ($anios as $a): ?>
        <li class="nav-item"><a class="nav-link <?= $a === $anio ? 'active' : '' ?>" href="<?= site_url('panel/feriados?anio=' . $a) ?>"><?= $a ?></a></li>
    <?php endforeach ?>
</ul>

<?php if ($conteo['total'] === 0): ?>
    <div class="alert ig-alerta-invitados d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span><i class="bi bi-info-circle me-1"></i>La gestión <strong><?= $anio ?></strong> no tiene feriados registrados.
            Mientras tanto el sistema usa el calendario calculado automáticamente.</span>
    </div>
<?php endif ?>

<?php if ($faltantes !== []): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="small">
                <strong><i class="bi bi-magic text-ig-osc me-1"></i>Feriados sugeridos para <?= $anio ?>:</strong>
                <?= count($faltantes) ?> del calendario boliviano aún no están registrados
                <span class="text-muted">(<?= esc(implode(', ', array_map(static fn ($p) => $p['nombre'] . ' ' . vac_fecha($p['fecha'], 'd MMM'), array_slice($faltantes, 0, 4)))) ?><?= count($faltantes) > 4 ? '…' : '' ?>)</span>
            </div>
            <?= form_open('panel/feriados/generar', ['class' => 'd-inline']) ?>
                <input type="hidden" name="anio" value="<?= $anio ?>">
                <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill px-3"><i class="bi bi-calendar-plus me-1"></i>Generar feriados de <?= $anio ?></button>
            <?= form_close() ?>
        </div>
    </div>
<?php endif ?>

<!-- Indicadores -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['', 'bi-calendar-event', $conteo['total'], 'Días registrados', ''],
        ['nacional', 'bi-flag', $conteo['nacional'], 'Feriados nacionales', ''],
        ['departamental', 'bi-geo-alt', $conteo['departamental'], 'Departamentales', 'ig-ind-verde'],
        ['no_laborable', 'bi-cup-hot', $conteo['no_laborable'], 'No laborables', 'ig-ind-ambar'],
    ] as [$t, $ico, $n, $txt, $cls]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= site_url('panel/feriados?anio=' . $anio . ($t ? '&tipo=' . $t : '')) ?>" class="ig-indicador <?= $cls ?> <?= $tipo === $t ? 'activo' : '' ?>">
                <i class="bi <?= $ico ?>"></i>
                <div><span class="ig-indicador-num"><?= (int) $n ?></span><span class="ig-indicador-txt"><?= $txt ?></span></div>
            </a>
        </div>
    <?php endforeach ?>
</div>
<p class="small text-muted mb-3"><i class="bi bi-info-circle me-1"></i><?= (int) $conteo['habiles'] ?> de estos días caen de lunes a viernes (son los que afectan el cálculo de vacaciones).</p>

<?php if ($feriados === []): ?>
    <div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">
        <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>No hay días registrados<?= $tipo ? ' de este tipo' : '' ?> en <?= $anio ?>.</div></div>
<?php endif ?>

<div class="row g-4">
    <?php foreach ($porMes as $mes => $lista): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <h2 class="h6 fw-semibold text-capitalize mb-2 ig-mes"><?= esc(vac_fecha(sprintf('%d-%02d-01', $anio, $mes), 'MMMM')) ?></h2>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($lista as $f): ?>
                            <?php $finde = (int) date('N', strtotime($f['fecha'])) >= 6; $pasado = $f['fecha'] < $hoy; ?>
                            <li class="ig-feriado <?= $pasado ? 'ig-feriado-pasado' : '' ?>">
                                <div class="ig-feriado-dia <?= $clsTipo[$f['tipo']] ?>">
                                    <span><?= (int) substr($f['fecha'], 8, 2) ?></span><small><?= esc(vac_fecha($f['fecha'], 'EEE')) ?></small>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate" title="<?= esc($f['nombre'], 'attr') ?>"><?= esc($f['nombre']) ?></div>
                                    <div class="small text-muted">
                                        <?= esc($tipos[$f['tipo']]) ?><?= $finde ? ' · <span class="text-warning-emphasis">fin de semana</span>' : '' ?>
                                        <?php if ($f['observacion']): ?><span class="d-block text-truncate" title="<?= esc($f['observacion'], 'attr') ?>"><?= esc($f['observacion']) ?></span><?php endif ?>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle ig-btn-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Acciones de <?= esc($f['nombre'], 'attr') ?>">
                                        <i class="bi bi-three-dots-vertical"></i></button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <a class="dropdown-item" href="<?= site_url('panel/feriados/' . (int) $f['idferiado'] . '/editar') ?>"><i class="bi bi-pencil-square me-2"></i>Editar</a>
                                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar"
                                                data-url="<?= site_url('panel/feriados/' . (int) $f['idferiado'] . '/eliminar') ?>"
                                                data-texto="<?= esc('«' . $f['nombre'] . '» del ' . vac_fecha($f['fecha']) . ' pasará a ser un día hábil. Las vacaciones que incluyan esa fecha se recalcularán.', 'attr') ?>">
                                            <i class="bi bi-trash me-2"></i>Quitar feriado</button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <?= form_open('', ['id' => 'formEliminar']) ?>
                <div class="modal-header border-0">
                    <h2 class="modal-title h5" id="modalEliminarTitulo"><i class="bi bi-calendar-x text-danger me-2"></i>Quitar feriado</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body pt-0" id="eliminarTexto"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-3">Quitar</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function (e) {
    document.getElementById('formEliminar').action = e.relatedTarget.dataset.url;
    document.getElementById('eliminarTexto').textContent = e.relatedTarget.dataset.texto;
});
</script>
<?= $this->endSection() ?>
