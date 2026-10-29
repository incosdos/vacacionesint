<?= $this->extend('layouts/panel') ?>
<?php $this->setVar('migas', ['Panel' => 'panel', 'Mis vacaciones' => 'panel/vacaciones', 'Solicitar' => null]); ?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>
<h1 class="h3 fw-bold mb-4">Solicitar vacaciones</h1>

<?php $err = session('errores') ?? []; ?>
<?= form_open('panel/vacaciones', ['id' => 'formVac', 'novalidate' => true]) ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="fecha_inicio" class="form-label">Desde</label>
                        <input type="date" class="form-control <?= isset($err['fecha_inicio']) ? 'is-invalid' : '' ?>" id="fecha_inicio" name="fecha_inicio"
                               value="<?= esc(old('fecha_inicio')) ?>" min="<?= $minimo ?>" max="<?= $maximo ?>" required>
                        <div class="invalid-feedback"><?= esc($err['fecha_inicio'] ?? 'Indique la fecha de inicio.') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label for="fecha_fin" class="form-label">Hasta</label>
                        <input type="date" class="form-control <?= isset($err['fecha_fin']) ? 'is-invalid' : '' ?>" id="fecha_fin" name="fecha_fin"
                               value="<?= esc(old('fecha_fin')) ?>" min="<?= $minimo ?>" max="<?= $maximo ?>" required>
                        <div class="invalid-feedback"><?= esc($err['fecha_fin'] ?? 'Indique la fecha final.') ?></div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="medio_dia" name="medio_dia" value="1" <?= old('medio_dia') ? 'checked' : '' ?> disabled>
                            <label class="form-check-label" for="medio_dia">Solo medio día <span class="text-muted small">(cuando inicio y fin son el mismo día)</span></label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="motivo" class="form-label">Motivo o comentario <span class="text-muted small">(opcional)</span></label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" maxlength="255" placeholder="Ej.: viaje familiar"><?= esc(old('motivo')) ?></textarea>
                    </div>
                </div>
                <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>
                    <?= $cfg->diasAnticipacion > 0 ? "Solicite con al menos {$cfg->diasAnticipacion} día(s) de anticipación. " : '' ?>
                    Máximo <?= (int) $cfg->maxDiasPorSolicitud ?> días hábiles por solicitud. Sábados, domingos y feriados no se descuentan.</p>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm ig-sticky ig-resumen">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-calculator me-1 text-ig-osc"></i>Resumen</h2>
                <div class="text-center py-2">
                    <div class="ig-resumen-num" id="resDias">0</div>
                    <div class="text-muted small">días hábiles a descontar</div>
                </div>
                <ul class="list-unstyled small mb-3" id="resDetalle"></ul>
                <div id="resSaldo" class="small"></div>
                <div id="resAviso" class="alert alert-warning py-2 small d-none mt-3 mb-0"></div>
                <button type="submit" class="btn btn-ig w-100 py-2 mt-3" id="btnEnviar" disabled><i class="bi bi-send me-1"></i>Enviar solicitud</button>
                <a href="<?= site_url('panel/vacaciones') ?>" class="btn btn-light rounded-pill w-100 mt-2">Cancelar</a>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const FERIADOS = <?= $feriadosJson ?>;
    const SALDOS = <?= json_encode($saldos, JSON_HEX_TAG) ?>;
    const SABADO_HABIL = <?= $cfg->sabadoHabil ? 'true' : 'false' ?>;
    const BLOQUEAR = <?= $cfg->bloquearSinSaldo ? 'true' : 'false' ?>;
    const MAX = <?= (int) $cfg->maxDiasPorSolicitud ?>;
    const ini = document.getElementById('fecha_inicio'), fin = document.getElementById('fecha_fin');
    const medio = document.getElementById('medio_dia'), btn = document.getElementById('btnEnviar');
    const fmt = new Intl.DateTimeFormat('es-BO', { weekday: 'short', day: 'numeric', month: 'short' });
    const num = n => String(n).replace('.', ',');
    const fecha = s => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };
    const iso = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');

    function calcular() {
        const det = document.getElementById('resDetalle'), aviso = document.getElementById('resAviso'), sal = document.getElementById('resSaldo');
        det.innerHTML = ''; sal.innerHTML = ''; aviso.classList.add('d-none');
        if (ini.value && (!fin.value || fin.value < ini.value)) fin.value = ini.value;
        fin.min = ini.value || ini.min;
        medio.disabled = !(ini.value && ini.value === fin.value);
        if (medio.disabled) medio.checked = false;
        if (!ini.value || !fin.value) { document.getElementById('resDias').textContent = '0'; btn.disabled = true; return; }

        let habiles = 0, finde = 0; const fer = [];
        for (let d = fecha(ini.value); d <= fecha(fin.value); d.setDate(d.getDate() + 1)) {
            const dow = d.getDay(), k = iso(d);
            if (dow === 0 || (dow === 6 && !SABADO_HABIL)) { finde++; continue; }
            if (FERIADOS[k]) { fer.push([k, FERIADOS[k]]); continue; }
            habiles++;
        }
        const dias = (medio.checked && habiles === 1) ? 0.5 : habiles;
        document.getElementById('resDias').textContent = num(dias);

        const total = Math.round((fecha(fin.value) - fecha(ini.value)) / 864e5) + 1;
        det.insertAdjacentHTML('beforeend', `<li class="d-flex justify-content-between py-1 border-bottom"><span>Días calendario</span><span>${total}</span></li>`);
        if (finde) det.insertAdjacentHTML('beforeend', `<li class="d-flex justify-content-between py-1 border-bottom"><span>Fines de semana</span><span>${finde}</span></li>`);
        fer.forEach(([k, n]) => det.insertAdjacentHTML('beforeend',
            `<li class="d-flex justify-content-between gap-2 py-1 border-bottom"><span><i class="bi bi-star text-ig-osc me-1"></i>${n}</span><span class="text-nowrap">${fmt.format(fecha(k))}</span></li>`));

        let problema = '';
        const gestion = ini.value.slice(0, 4), s = SALDOS[gestion];
        if (ini.value.slice(0, 4) !== fin.value.slice(0, 4)) problema = 'La solicitud no puede abarcar dos gestiones. Divídala en dos solicitudes.';
        else if (dias === 0) problema = 'El periodo no tiene días hábiles.';
        else if (dias > MAX) problema = `Una solicitud no puede superar ${MAX} días hábiles.`;
        if (s && s.disponibles !== null) {
            const queda = s.disponibles - dias;
            sal.innerHTML = `<div class="d-flex justify-content-between"><span>Disponibles gestión ${gestion}</span><strong>${num(s.disponibles)}</strong></div>
                <div class="d-flex justify-content-between"><span>Le quedarían</span><strong class="${queda < 0 ? 'text-danger' : 'text-ig-osc'}">${num(Math.max(queda, 0))}</strong></div>`;
            if (!problema && queda < 0) problema = s.asignados === 0
                ? 'Aún no le corresponden vacaciones: se adquieren al cumplir un año de antigüedad.'
                : `Supera su saldo disponible (${num(s.disponibles)} días).`;
        } else if (s) {
            sal.innerHTML = '<span class="text-muted">Saldo no disponible (fecha de ingreso sin registrar).</span>';
        }
        if (problema) { aviso.textContent = problema; aviso.classList.remove('d-none'); }
        btn.disabled = !!problema && (BLOQUEAR || !problema.startsWith('Supera'));
    }
    [ini, fin, medio].forEach(el => el.addEventListener('change', calcular));
    calcular();
})();
</script>
<?= $this->endSection() ?>
