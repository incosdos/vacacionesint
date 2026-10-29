<?= $this->extend('layouts/panel') ?>
<?php
$this->setVar('migas', ['Panel' => 'panel', 'Reportes' => null]);
$n = static fn ($v) => $v === null ? '—' : rtrim(rtrim(number_format((float) $v, 1, ',', '.'), '0'), ',');
$qs = http_build_query(array_filter(['gestion' => $f['gestion'], 'q' => $f['q'], 'rol' => $f['rol'], 'cargo' => $f['cargo'], 'inactivos' => $f['inactivos'] ? '1' : null]));
$mesesNombre = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
$rolNombre = '';
foreach ($roles as $r) { if ((string) $r['idrol'] === $f['rol']) { $rolNombre = $r['nombre']; } }
$cargoNombre = $f['cargo'] === 'sin' ? 'Sin cargo' : '';
foreach ($cargos as $c) { if ((string) $c['idcargo'] === $f['cargo']) { $cargoNombre = $c['nombre']; } }
?>

<?= $this->section('contenido') ?>
<div class="ig-solo-impresion ig-impresion-cabecera">
    <img src="<?= base_url('assets/img/marca/logo-intergraphics.png') ?>" alt="intergraphics" height="40">
    <div class="text-end small">
        <strong><?= esc($empresa->razonSocial) ?></strong><br>
        Emitido el <?= date('d/m/Y H:i') ?> por <?= esc(session('nombre')) ?>
    </div>
</div>

<div class="ig-no-imprimir"><?= $this->include('partials/migas') ?></div>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Resumen de vacaciones del personal</h1>
        <p class="text-muted mb-0">Gestión <?= (int) $f['gestion'] ?>
            <?= $rolNombre ? ' · Rol: ' . esc($rolNombre) : '' ?><?= $cargoNombre ? ' · Cargo: ' . esc($cargoNombre) : '' ?><?= $f['q'] !== '' ? ' · Búsqueda: «' . esc($f['q']) . '»' : '' ?>
            <?= $f['inactivos'] ? ' · Incluye inactivos' : '' ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2 ig-no-imprimir">
        <a href="<?= site_url('panel/reportes/exportar?' . $qs) ?>" class="btn btn-outline-dark rounded-pill px-3"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel</a>
        <button type="button" class="btn btn-ig px-3" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir / PDF</button>
    </div>
</div>

<!-- Filtros -->
<form method="get" action="<?= site_url('panel/reportes') ?>" class="card border-0 shadow-sm mb-4 ig-no-imprimir">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label for="gestion" class="form-label small mb-1">Gestión</label>
                <select class="form-select" id="gestion" name="gestion">
                    <?php foreach ($gestiones as $g): ?>
                        <option value="<?= $g ?>" <?= $g === $f['gestion'] ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="rol" class="form-label small mb-1">Rol</label>
                <select class="form-select" id="rol" name="rol">
                    <option value="">Todos</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= (int) $r['idrol'] ?>" <?= $f['rol'] === (string) $r['idrol'] ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="cargo" class="form-label small mb-1">Cargo</label>
                <select class="form-select" id="cargo" name="cargo">
                    <option value="">Todos</option>
                    <option value="sin" <?= $f['cargo'] === 'sin' ? 'selected' : '' ?>>Sin cargo</option>
                    <?php foreach ($cargos as $c): ?>
                        <option value="<?= (int) $c['idcargo'] ?>" <?= $f['cargo'] === (string) $c['idcargo'] ? 'selected' : '' ?>><?= esc($c['nombre']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="q" class="form-label small mb-1">Persona</label>
                <input type="search" class="form-control" id="q" name="q" value="<?= esc($f['q']) ?>" placeholder="Nombre o CI">
            </div>
            <div class="col-md-2">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="inactivos" name="inactivos" value="1" <?= $f['inactivos'] ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="inactivos">Incluir inactivos</label>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-ig flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i>Ver</button>
                <?php if ($f['q'] !== '' || $f['rol'] !== '' || $f['cargo'] !== '' || $f['inactivos']): ?>
                    <a class="btn btn-light" href="<?= site_url('panel/reportes?gestion=' . $f['gestion']) ?>" aria-label="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                <?php endif ?>
            </div>
        </div>
    </div>
</form>

<!-- Indicadores -->
<div class="row g-3 mb-4">
    <?php
    $kpis = [
        ['bi-people', $t['personas'], 'Personas', ''],
        ['bi-calendar-check', $n($t['autorizados']), 'Días autorizados', 'de ' . $n($t['asignados']) . ' que corresponden'],
        ['bi-hourglass-split', $n($t['pendientes']), 'Días en revisión', $t['solicitudes_pend'] . ' solicitud(es) pendiente(s)'],
        ['bi-piggy-bank', $n($t['disponibles']), 'Días disponibles', 'saldo por usar en la gestión'],
        ['bi-pie-chart', $t['uso'] === null ? '—' : $t['uso'] . '%', 'Uso de vacaciones', $t['sin_tomar'] . ' persona(s) sin tomar ningún día'],
        ['bi-sun', $t['de_vacaciones'], 'De vacaciones hoy', $t['sin_fecha'] ? $t['sin_fecha'] . ' sin fecha de ingreso' : ''],
    ];
    ?>
    <?php foreach ($kpis as [$ico, $val, $txt, $sub]): ?>
        <div class="col-6 col-lg-4 col-xl-2 ig-kpi-col">
            <div class="ig-kpi h-100">
                <i class="bi <?= $ico ?>"></i>
                <span class="ig-kpi-num"><?= $val ?></span>
                <span class="ig-kpi-txt"><?= $txt ?></span>
                <?php if ($sub): ?><span class="ig-kpi-sub"><?= esc($sub) ?></span><?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
</div>

<div class="row g-4 mb-4 ig-fila-grafico">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 position-relative overflow-hidden">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-bar-chart me-1 text-ig-osc"></i>Días hábiles de vacación autorizados por mes</h2>
                <div class="ig-grafico"><canvas id="graficoMeses" aria-label="Días autorizados por mes" role="img"></canvas></div>
                <table class="visually-hidden">
                    <caption>Días autorizados por mes</caption>
                    <tr><?php foreach ($mesesNombre as $i => $m): ?><th scope="col"><?= $m ?></th><?php endforeach ?></tr>
                    <tr><?php foreach ($meses as $v): ?><td><?= $n($v) ?></td><?php endforeach ?></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-airplane me-1 text-ig-osc"></i>Ausencias en los próximos 30 días</h2>
                <?php if ($f['gestion'] !== (int) date('Y')): ?>
                    <p class="small text-muted mb-0">Disponible solo para la gestión actual.</p>
                <?php elseif ($proximas === []): ?>
                    <p class="small text-muted mb-0"><i class="bi bi-check2-circle text-success me-1"></i>No hay vacaciones autorizadas en los próximos 30 días.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0 ig-proximas">
                        <?php foreach ($proximas as $p): ?>
                            <?php $enCurso = $p['fecha_inicio'] <= date('Y-m-d'); ?>
                            <li>
                                <div class="d-flex justify-content-between gap-2">
                                    <span class="fw-semibold small"><?= esc($p['nombre']) ?></span>
                                    <?php if ($enCurso): ?><span class="badge rounded-pill ig-badge-cont">Hoy</span><?php endif ?>
                                </div>
                                <div class="small text-muted"><?= esc(vac_periodo($p['fecha_inicio'], $p['fecha_fin'])) ?> · <?= vac_dias($p['dias_habiles']) ?></div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- Detalle por persona -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 pb-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="h6 fw-semibold mb-0"><i class="bi bi-table me-1 text-ig-osc"></i>Detalle por persona</h2>
            <span class="small text-muted ig-no-imprimir">Haga clic en un encabezado para ordenar</span>
        </div>
    </div>
    <?php if ($filas === []): ?>
        <div class="card-body text-center text-muted py-5">No hay personas con esos criterios.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0 ig-tabla ig-reporte" id="tablaReporte">
                <thead>
                    <tr>
                        <th scope="col" data-orden="texto">Colaborador</th>
                        <th scope="col" data-orden="texto" class="d-none d-md-table-cell">Cargo</th>
                        <th scope="col" data-orden="num" class="text-center d-none d-lg-table-cell">Antigüedad</th>
                        <th scope="col" data-orden="num" class="text-center">Corresp.</th>
                        <th scope="col" data-orden="num" class="text-center">Usados</th>
                        <th scope="col" data-orden="num" class="text-center d-none d-sm-table-cell">Pend.</th>
                        <th scope="col" data-orden="num" class="text-center">Disp.</th>
                        <th scope="col" data-orden="num" class="d-none d-md-table-cell" style="min-width:150px">Uso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $r): ?>
                        <tr>
                            <td data-v="<?= esc($r['apellidos'] . ' ' . $r['nombres'], 'attr') ?>">
                                <span class="fw-semibold d-block"><?= esc($r['nombres'] . ' ' . $r['apellidos']) ?>
                                    <?php if ((int) $r['de_vacaciones']): ?><i class="bi bi-sun text-warning" title="De vacaciones hoy"></i><?php endif ?>
                                    <?php if ((int) $r['estado'] !== 1): ?><span class="badge text-bg-light">Inactivo</span><?php endif ?></span>
                                <span class="small text-muted">CI <?= esc($r['ci']) ?><?= $r['fecha_ingreso'] ? ' · desde ' . ig_fecha($r['fecha_ingreso']) : '' ?></span>
                            </td>
                            <td class="d-none d-md-table-cell small" data-v="<?= esc($r['cargo'] ?? 'zzz', 'attr') ?>"><?= $r['cargo'] ? esc($r['cargo']) : '<span class="text-muted">Sin cargo</span>' ?>
                                <span class="d-block text-muted" style="font-size:.72rem"><?= esc($r['rol']) ?></span></td>
                            <td class="text-center d-none d-lg-table-cell small" data-v="<?= $r['antiguedad'] ? $r['antiguedad']['anios'] * 12 + $r['antiguedad']['meses'] : -1 ?>">
                                <?= $r['antiguedad'] ? $r['antiguedad']['anios'] . ' a ' . $r['antiguedad']['meses'] . ' m' : '<span class="text-warning-emphasis" title="Sin fecha de ingreso">—</span>' ?></td>
                            <td class="text-center" data-v="<?= $r['asignados'] ?? -1 ?>"><?= $n($r['asignados']) ?></td>
                            <td class="text-center fw-semibold" data-v="<?= $r['autorizados'] ?>"><?= $n($r['autorizados']) ?></td>
                            <td class="text-center d-none d-sm-table-cell" data-v="<?= $r['pendientes'] ?>"><?= $r['pendientes'] > 0 ? '<span class="ig-vac-estado ig-vac-pendiente">' . $n($r['pendientes']) . '</span>' : '0' ?></td>
                            <td class="text-center fw-semibold text-ig-osc" data-v="<?= $r['disponibles'] ?? -1 ?>"><?= $n($r['disponibles']) ?></td>
                            <td class="d-none d-md-table-cell" data-v="<?= $r['uso'] ?? -1 ?>">
                                <?php if ($r['uso'] === null): ?>
                                    <span class="small text-muted"><?= $r['asignados'] === 0 ? 'Menos de 1 año' : 'Sin dato' ?></span>
                                <?php else: ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress ig-progress flex-grow-1" role="progressbar" aria-valuenow="<?= $r['uso'] ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Uso">
                                            <div class="progress-bar" style="width: <?= $r['uso'] ?>%"></div>
                                        </div>
                                        <span class="small text-muted" style="width:36px"><?= $r['uso'] ?>%</span>
                                    </div>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr class="fw-semibold">
                        <td>Total (<?= (int) $t['personas'] ?>)</td>
                        <td class="d-none d-md-table-cell"></td>
                        <td class="d-none d-lg-table-cell"></td>
                        <td class="text-center"><?= $n($t['asignados']) ?></td>
                        <td class="text-center"><?= $n($t['autorizados']) ?></td>
                        <td class="text-center d-none d-sm-table-cell"><?= $n($t['pendientes']) ?></td>
                        <td class="text-center text-ig-osc"><?= $n($t['disponibles']) ?></td>
                        <td class="d-none d-md-table-cell small"><?= $t['uso'] === null ? '' : $t['uso'] . '% de uso' ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="small text-muted px-4 py-3 mb-0">
            Días hábiles de la gestión <?= (int) $f['gestion'] ?>. «Corresp.» según antigüedad (Ley General del Trabajo);
            «Disp.» descuenta lo autorizado y lo pendiente.
        </p>
    <?php endif ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
(function () {
    // Gráfico mensual
    const datos = <?= json_encode(array_values($meses)) ?>;
    const lienzo = document.getElementById('graficoMeses');
    if (window.Chart && lienzo) {
        const grafico = new Chart(lienzo, {
            type: 'bar',
            data: {
                labels: <?= json_encode($mesesNombre) ?>,
                datasets: [{ label: 'Días autorizados', data: datos, backgroundColor: '#00a3b6', hoverBackgroundColor: '#00818e', borderRadius: 6, maxBarThickness: 38 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + String(c.parsed.y).replace('.', ',') + ' días' } } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef1f3' } }, x: { grid: { display: false } } }
            }
        });
        // Ajustar el gráfico al ancho de la hoja al imprimir o guardar en PDF
        const ajustar = () => { grafico.resize(); };
        window.addEventListener('beforeprint', ajustar);
        window.addEventListener('afterprint', ajustar);
        window.matchMedia('print').addEventListener('change', ajustar);
    }

    // Ordenar la tabla
    const tabla = document.getElementById('tablaReporte');
    if (!tabla) return;
    tabla.querySelectorAll('th[data-orden]').forEach(function (th, col) {
        th.setAttribute('role', 'button');
        th.addEventListener('click', function () {
            const asc = th.dataset.dir !== 'asc';
            tabla.querySelectorAll('th').forEach(h => { delete h.dataset.dir; h.removeAttribute('aria-sort'); });
            th.dataset.dir = asc ? 'asc' : 'desc';
            th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');
            const filas = [...tabla.tBodies[0].rows];
            filas.sort(function (a, b) {
                const x = a.cells[col].dataset.v, y = b.cells[col].dataset.v;
                const r = th.dataset.orden === 'num' ? parseFloat(x) - parseFloat(y) : x.localeCompare(y, 'es');
                return asc ? r : -r;
            });
            filas.forEach(f => tabla.tBodies[0].appendChild(f));
        });
    });
})();
</script>
<?= $this->endSection() ?>
