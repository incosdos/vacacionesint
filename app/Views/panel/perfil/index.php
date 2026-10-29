<?= $this->extend('layouts/panel') ?>
<?php
$this->setVar('migas', ['Panel' => 'panel', 'Mi perfil' => null]);
$err = session('errores') ?? [];
$inv = static fn (string $c) => isset($err[$c]) ? 'is-invalid' : '';
[$estTxt, $estCls, $estIco] = ig_estado_usuario($u);
?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>

<div class="card border-0 shadow-sm mb-4 ig-perfil">
    <div class="card-body p-4 d-flex flex-wrap align-items-center gap-3">
        <span class="ig-avatar ig-avatar-lg ig-avatar-<?= (int) $u['idrol'] % 5 ?>"><?= esc(ig_iniciales($u)) ?></span>
        <div class="flex-grow-1">
            <h1 class="h4 fw-bold mb-1"><?= esc($u['nombres'] . ' ' . $u['apellidos']) ?></h1>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge rounded-pill ig-badge-rol <?= (int) $u['idrol'] === 4 ? 'ig-badge-invitado' : '' ?>"><?= esc($u['rol']) ?></span>
                <span class="ig-estado <?= $estCls ?>"><i class="bi <?= $estIco ?> me-1"></i><?= $estTxt ?></span>
                <span class="small text-muted"><i class="bi bi-briefcase me-1"></i><?= esc($u['cargo'] ?? 'Sin cargo') ?></span>
                <span class="small text-muted"><i class="bi bi-envelope me-1"></i><?= esc($u['email']) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <ul class="nav nav-pills ig-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $pestana === 'datos' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tabDatos" type="button" role="tab"
                        aria-controls="tabDatos" aria-selected="<?= $pestana === 'datos' ? 'true' : 'false' ?>"><i class="bi bi-person me-1"></i>Mis datos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $pestana === 'seguridad' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tabSeguridad" type="button" role="tab"
                        aria-controls="tabSeguridad" aria-selected="<?= $pestana === 'seguridad' ? 'true' : 'false' ?>"><i class="bi bi-shield-lock me-1"></i>Contraseña</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Datos -->
            <div class="tab-pane fade <?= $pestana === 'datos' ? 'show active' : '' ?>" id="tabDatos" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?= form_open('panel/perfil', ['id' => 'formDatos', 'novalidate' => true]) ?>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="nombres" class="form-label">Nombres</label>
                                    <input type="text" class="form-control <?= $inv('nombres') ?>" id="nombres" name="nombres" maxlength="100" required
                                           value="<?= esc(old('nombres', $u['nombres'])) ?>" autocomplete="given-name">
                                    <div class="invalid-feedback"><?= esc($err['nombres'] ?? 'Ingrese sus nombres.') ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control <?= $inv('apellidos') ?>" id="apellidos" name="apellidos" maxlength="100" required
                                           value="<?= esc(old('apellidos', $u['apellidos'])) ?>" autocomplete="family-name">
                                    <div class="invalid-feedback"><?= esc($err['apellidos'] ?? 'Ingrese sus apellidos.') ?></div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="celular" class="form-label">Celular</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                        <input type="tel" inputmode="numeric" pattern="[0-9]{7,15}" class="form-control <?= $inv('celular') ?>" id="celular" name="celular"
                                               maxlength="15" required value="<?= esc((string) old('celular', (string) $u['celular'])) ?>" autocomplete="tel">
                                        <div class="invalid-feedback"><?= esc($err['celular'] ?? 'Ingrese de 7 a 15 dígitos.') ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="ciRO">Cédula de identidad</label>
                                    <input type="text" class="form-control" id="ciRO" value="<?= esc($u['ci']) ?>" readonly disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="emailRO">Correo electrónico</label>
                                    <input type="email" class="form-control" id="emailRO" value="<?= esc($u['email']) ?>" readonly disabled>
                                    <div class="form-text"><i class="bi bi-lock me-1"></i>El CI y el correo identifican su cuenta. Para cambiarlos, solicítelo al administrador.</div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-ig px-4"><i class="bi bi-check2-circle me-1"></i>Guardar cambios</button>
                                </div>
                            </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="tab-pane fade <?= $pestana === 'seguridad' ? 'show active' : '' ?>" id="tabSeguridad" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?= form_open('panel/perfil/password', ['id' => 'formPass', 'novalidate' => true]) ?>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="password_actual" class="form-label">Contraseña actual</label>
                                    <div class="input-group has-validation">
                                        <input type="password" class="form-control <?= $inv('password_actual') ?>" id="password_actual" name="password_actual"
                                               autocomplete="current-password" required>
                                        <button class="btn btn-outline-secondary" type="button" data-ver-password="password_actual" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                                        <div class="invalid-feedback"><?= esc($err['password_actual'] ?? 'Ingrese su contraseña actual.') ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="password" class="form-label">Nueva contraseña</label>
                                    <div class="input-group has-validation">
                                        <input type="password" class="form-control <?= $inv('password') ?>" id="password" name="password" minlength="8" maxlength="72"
                                               autocomplete="new-password" required>
                                        <button class="btn btn-outline-secondary" type="button" data-ver-password="password" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                                        <div class="invalid-feedback"><?= esc($err['password'] ?? 'Mínimo 8 caracteres, con letras y números.') ?></div>
                                    </div>
                                    <div class="ig-fuerza mt-2" aria-hidden="true"><span id="fuerzaBarra"></span></div>
                                    <div class="form-text" id="fuerzaTxt">Mínimo 8 caracteres, con letras y números.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="password_confirm" class="form-label">Confirmar nueva contraseña</label>
                                    <div class="input-group has-validation">
                                        <input type="password" class="form-control <?= $inv('password_confirm') ?>" id="password_confirm" name="password_confirm"
                                               maxlength="72" autocomplete="new-password" required>
                                        <button class="btn btn-outline-secondary" type="button" data-ver-password="password_confirm" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                                        <div class="invalid-feedback"><?= esc($err['password_confirm'] ?? 'Las contraseñas no coinciden.') ?></div>
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-ig px-4"><i class="bi bi-key me-1"></i>Cambiar contraseña</button>
                                </div>
                            </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-person-vcard me-1 text-ig-osc"></i>Mi cuenta</h2>
                <dl class="row mb-0 small ig-dl">
                    <dt class="col-6">Cargo</dt>
                    <dd class="col-6"><?= $u['cargo'] ? esc($u['cargo']) : '<span class="text-muted">Sin asignar</span>' ?></dd>
                    <dt class="col-6">Fecha de ingreso</dt>
                    <dd class="col-6"><?= $u['fecha_ingreso'] ?? null ? ig_fecha($u['fecha_ingreso']) : '<span class="text-muted">Sin registrar</span>' ?></dd>
                    <?php if ($antig !== null): ?>
                        <dt class="col-6">Antigüedad</dt>
                        <dd class="col-6"><?= $antig['anios'] ?> año<?= $antig['anios'] === 1 ? '' : 's' ?><?= $antig['meses'] ? ', ' . $antig['meses'] . ' mes' . ($antig['meses'] === 1 ? '' : 'es') : '' ?></dd>
                    <?php endif ?>
                    <dt class="col-6">Registrado</dt><dd class="col-6"><?= ig_fecha($u['fechaderegistro']) ?></dd>
                    <dt class="col-6">Último acceso</dt><dd class="col-6"><?= ig_fecha($u['ultimo_acceso'], true) ?></dd>
                </dl>
            </div>
        </div>

        <?php if ($saldo !== null): ?>
            <a href="<?= site_url('panel/vacaciones') ?>" class="ig-saldo ig-saldo-destacado ig-saldo-enlace d-block mb-4 text-decoration-none">
                <span class="ig-saldo-num"><?= $saldo['disponibles'] === null ? '—' : rtrim(rtrim(number_format($saldo['disponibles'], 1, ',', ''), '0'), ',') ?></span>
                <span class="ig-saldo-txt">días de vacación disponibles · gestión <?= (int) $saldo['gestion'] ?> <i class="bi bi-arrow-right"></i></span>
            </a>
        <?php endif ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-key me-1 text-ig-osc"></i>Lo que puedo hacer</h2>
                <ul class="list-unstyled small mb-0">
                    <?php foreach ($permisos as $p): ?>
                        <li class="py-1"><i class="bi bi-check2 text-success me-1"></i><?= esc($p['descripcion']) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    document.querySelectorAll('[data-ver-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const c = document.getElementById(btn.dataset.verPassword), oculto = c.type === 'password';
            c.type = oculto ? 'text' : 'password';
            btn.querySelector('i').className = oculto ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
    document.getElementById('celular').addEventListener('input', function () { this.value = this.value.replace(/\D/g, ''); });

    // Indicador de fortaleza
    const pass = document.getElementById('password'), conf = document.getElementById('password_confirm');
    const barra = document.getElementById('fuerzaBarra'), txt = document.getElementById('fuerzaTxt');
    pass.addEventListener('input', function () {
        const v = pass.value;
        let n = 0;
        if (v.length >= 8) n++;
        if (v.length >= 12) n++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) n++;
        if (/\d/.test(v)) n++;
        if (/[^A-Za-z0-9]/.test(v)) n++;
        const niveles = [['', ''], ['Muy débil', '#e05d5d'], ['Débil', '#f0a858'], ['Aceptable', '#c6d32f'], ['Buena', '#72ba6e'], ['Excelente', '#00818e']];
        const [t, c] = niveles[v ? Math.max(1, n) : 0];
        barra.style.width = (v ? Math.max(1, n) * 20 : 0) + '%';
        barra.style.background = c;
        txt.textContent = v ? 'Seguridad: ' + t : 'Mínimo 8 caracteres, con letras y números.';
    });

    // Validación en el navegador
    [['formDatos', null], ['formPass', function () {
        conf.setCustomValidity(conf.value !== pass.value ? 'No coincide' : '');
        pass.setCustomValidity(pass.value && !/^(?=.*[A-Za-z])(?=.*\d).{8,}$/.test(pass.value) ? 'Débil' : '');
    }]].forEach(function ([id, previo]) {
        const f = document.getElementById(id);
        f.addEventListener('submit', function (e) {
            if (previo) previo();
            f.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            if (!f.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
            f.classList.add('was-validated');
        });
    });
})();
</script>
<?= $this->endSection() ?>
