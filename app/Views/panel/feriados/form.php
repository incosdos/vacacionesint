<?= $this->extend('layouts/panel') ?>
<?php
$id  = $f['idferiado'] !== null ? (int) $f['idferiado'] : null;
$err = session('errores') ?? [];
$this->setVar('migas', ['Panel' => 'panel', 'Feriados' => 'panel/feriados', ($id === null ? 'Agregar día' : 'Editar') => null]);
$sugerencias = ['Feriado por decreto', 'Día no laborable por decreto', 'Aniversario de la empresa', 'Cierre de fin de año', 'Asueto'];
?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>
<h1 class="h3 fw-bold mb-4"><?= $id === null ? 'Agregar feriado o día no laborable' : 'Editar: ' . esc($f['nombre']) ?></h1>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <?= form_open($id === null ? 'panel/feriados' : "panel/feriados/{$id}", ['id' => 'formFeriado', 'novalidate' => true]) ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control <?= isset($err['fecha']) ? 'is-invalid' : '' ?>" id="fecha" name="fecha"
                                   value="<?= esc(old('fecha', $f['fecha'])) ?>" min="2000-01-01" max="2100-12-31" required>
                            <div class="invalid-feedback"><?= esc($err['fecha'] ?? 'Indique la fecha.') ?></div>
                            <div class="form-text" id="diaSemana"></div>
                        </div>
                        <div class="col-sm-6">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select <?= isset($err['tipo']) ? 'is-invalid' : '' ?>" id="tipo" name="tipo" required>
                                <?php foreach ($tipos as $k => $t): ?>
                                    <option value="<?= $k ?>" <?= old('tipo', $f['tipo']) === $k ? 'selected' : '' ?>><?= esc($t) ?></option>
                                <?php endforeach ?>
                            </select>
                            <div class="invalid-feedback"><?= esc($err['tipo'] ?? '') ?></div>
                        </div>
                        <div class="col-12">
                            <label for="nombre" class="form-label">Nombre o motivo</label>
                            <input type="text" class="form-control <?= isset($err['nombre']) ? 'is-invalid' : '' ?>" id="nombre" name="nombre" list="sugerencias"
                                   value="<?= esc(old('nombre', $f['nombre'])) ?>" minlength="3" maxlength="120" required placeholder="Ej.: Día del Estado Plurinacional">
                            <datalist id="sugerencias"><?php foreach ($sugerencias as $s): ?><option value="<?= esc($s, 'attr') ?>"><?php endforeach ?></datalist>
                            <div class="invalid-feedback"><?= esc($err['nombre'] ?? 'Ingrese al menos 3 caracteres.') ?></div>
                        </div>
                        <div class="col-12">
                            <label for="observacion" class="form-label">Observación <span class="text-muted small">(opcional)</span></label>
                            <input type="text" class="form-control <?= isset($err['observacion']) ? 'is-invalid' : '' ?>" id="observacion" name="observacion"
                                   value="<?= esc(old('observacion', $f['observacion'] ?? '')) ?>" maxlength="255" placeholder="Ej.: Decreto Supremo N° 5521">
                            <div class="invalid-feedback"><?= esc($err['observacion'] ?? '') ?></div>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                            <a href="<?= site_url('panel/feriados' . ($f['fecha'] ? '?anio=' . substr($f['fecha'], 0, 4) : '')) ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-ig px-4"><i class="bi bi-check2-circle me-1"></i>Guardar</button>
                        </div>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 small">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-info-circle me-1 text-ig-osc"></i>Tipos de día</h2>
                <p class="mb-2"><span class="ig-chip ig-fer-nacional-chip">Feriado nacional</span> Feriados de ley o decretados para todo el país.</p>
                <p class="mb-2"><span class="ig-chip ig-fer-departamental-chip">Feriado departamental</span> Solo en Cochabamba (14 de septiembre).</p>
                <p class="mb-3"><span class="ig-chip ig-fer-nolaborable-chip">Día no laborable</span> Días sin trabajo decididos por la empresa o por decreto (asuetos, cierres).</p>
                <p class="text-muted mb-0">Ninguno de estos días se descuenta de las vacaciones. Si la fecha cae dentro de vacaciones ya solicitadas o autorizadas, sus días hábiles se recalculan automáticamente.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const f = document.getElementById('formFeriado'), fecha = document.getElementById('fecha'), txt = document.getElementById('diaSemana');
    const fmt = new Intl.DateTimeFormat('es-BO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    const mostrar = () => {
        if (!fecha.value) { txt.textContent = ''; return; }
        const [y, m, d] = fecha.value.split('-').map(Number), dt = new Date(y, m - 1, d);
        txt.textContent = fmt.format(dt) + ([0, 6].includes(dt.getDay()) ? ' · cae en fin de semana (no afecta las vacaciones)' : '');
    };
    fecha.addEventListener('change', mostrar); mostrar();
    f.addEventListener('submit', function (e) {
        f.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        if (!f.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
        f.classList.add('was-validated');
    });
})();
</script>
<?= $this->endSection() ?>
