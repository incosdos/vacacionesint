<?= $this->extend('layouts/panel') ?>

<?php
$id    = $u['idusuario'] !== null ? (int) $u['idusuario'] : null;
$yo    = $id !== null && $id === (int) session('idusuario');
$err   = session('errores') ?? [];
$v     = static fn (string $campo) => old($campo, $u[$campo] ?? '');
$inv   = static fn (string $campo) => isset($err[$campo]) ? 'is-invalid' : '';
$this->setVar('migas', ['Panel' => 'panel', 'Usuarios' => 'panel/usuarios', ($id === null ? 'Nuevo usuario' : 'Editar') => null]);
?>

<?= $this->section('contenido') ?>
<?= $this->include('partials/migas') ?>

<h1 class="h3 fw-bold mb-4"><?= $id === null ? 'Nuevo usuario' : 'Editar: ' . esc($u['nombres'] . ' ' . $u['apellidos']) ?></h1>

<?php if ($err !== []): ?>
    <div class="alert alert-danger py-2"><i class="bi bi-exclamation-circle me-1"></i>Revise los campos marcados.</div>
<?php endif ?>

<?= form_open($id === null ? 'panel/usuarios' : "panel/usuarios/{$id}", ['id' => 'formUsuario', 'novalidate' => true]) ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-person-vcard me-1 text-ig-osc"></i>Datos personales</h2>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="nombres" class="form-label">Nombres</label>
                        <input type="text" class="form-control <?= $inv('nombres') ?>" id="nombres" name="nombres" value="<?= esc($v('nombres')) ?>" maxlength="100" required>
                        <div class="invalid-feedback"><?= esc($err['nombres'] ?? 'Ingrese los nombres.') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" class="form-control <?= $inv('apellidos') ?>" id="apellidos" name="apellidos" value="<?= esc($v('apellidos')) ?>" maxlength="100" required>
                        <div class="invalid-feedback"><?= esc($err['apellidos'] ?? 'Ingrese los apellidos.') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label for="ci" class="form-label">Cédula de identidad (CI)</label>
                        <input type="text" class="form-control <?= $inv('ci') ?>" id="ci" name="ci" value="<?= esc($v('ci')) ?>" maxlength="20" required>
                        <div class="invalid-feedback"><?= esc($err['ci'] ?? 'Ingrese el CI.') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="tel" inputmode="numeric" pattern="[0-9]{7,15}" class="form-control <?= $inv('celular') ?>" id="celular" name="celular"
                               value="<?= esc((string) $v('celular')) ?>" maxlength="15" required>
                        <div class="invalid-feedback"><?= esc($err['celular'] ?? 'Ingrese de 7 a 15 dígitos.') ?></div>
                    </div>
                    <div class="col-sm-6">
                        <label for="fecha_ingreso" class="form-label">Fecha de ingreso a la empresa</label>
                        <input type="date" class="form-control <?= $inv('fecha_ingreso') ?>" id="fecha_ingreso" name="fecha_ingreso"
                               value="<?= esc((string) $v('fecha_ingreso')) ?>" max="<?= date('Y-m-d') ?>">
                        <div class="invalid-feedback"><?= esc($err['fecha_ingreso'] ?? 'Fecha no válida.') ?></div>
                        <div class="form-text">Define los días de vacación por antigüedad.</div>
                    </div>
                    <div class="col-sm-6">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control <?= $inv('email') ?>" id="email" name="email" value="<?= esc($v('email')) ?>" maxlength="150" required>
                        <div class="invalid-feedback"><?= esc($err['email'] ?? 'Ingrese un correo válido.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-1"><i class="bi bi-lock me-1 text-ig-osc"></i>Contraseña</h2>
                <p class="small text-muted mb-3">
                    <?= $id === null ? 'Contraseña inicial. Compártala con la persona y pídale que la cambie.' : 'Déjela vacía para mantener la contraseña actual.' ?>
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="password" class="form-label"><?= $id === null ? 'Contraseña' : 'Nueva contraseña' ?></label>
                        <div class="input-group has-validation">
                            <input type="password" class="form-control <?= $inv('password') ?>" id="password" name="password" minlength="8" maxlength="72"
                                   autocomplete="new-password" <?= $id === null ? 'required' : '' ?>>
                            <button class="btn btn-outline-secondary" type="button" id="generar" title="Generar contraseña segura"><i class="bi bi-magic"></i></button>
                            <div class="invalid-feedback"><?= esc($err['password'] ?? 'Mínimo 8 caracteres, con letras y números.') ?></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="password_confirm" class="form-label">Confirmar</label>
                        <input type="password" class="form-control <?= $inv('password_confirm') ?>" id="password_confirm" name="password_confirm" maxlength="72" autocomplete="new-password">
                        <div class="invalid-feedback"><?= esc($err['password_confirm'] ?? 'Las contraseñas no coinciden.') ?></div>
                    </div>
                    <div class="col-12 d-none" id="generadaBox">
                        <div class="alert alert-info py-2 small mb-0">Contraseña generada: <code class="fs-6" id="generadaTxt"></code> — cópiela antes de guardar.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm ig-sticky">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-shield-lock me-1 text-ig-osc"></i>Acceso</h2>

                <div class="mb-3">
                    <label for="idrol" class="form-label">Rol</label>
                    <select class="form-select <?= $inv('idrol') ?>" id="idrol" name="idrol" <?= $yo ? 'disabled' : '' ?>>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= (int) $r['idrol'] ?>" <?= (string) $v('idrol') === (string) $r['idrol'] ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
                        <?php endforeach ?>
                    </select>
                    <?php if ($yo): ?>
                        <input type="hidden" name="idrol" value="<?= (int) $u['idrol'] ?>">
                        <div class="form-text">No puede cambiar su propio rol.</div>
                    <?php endif ?>
                    <div class="invalid-feedback"><?= esc($err['idrol'] ?? '') ?></div>
                </div>

                <div class="mb-3">
                    <label for="idcargo" class="form-label">Cargo</label>
                    <?php $puedeCargo = tiene_permiso('cargos.gestionar'); ?>
                    <select class="form-select <?= $inv('idcargo') ?>" id="idcargo" name="idcargo" <?= $puedeCargo ? '' : 'disabled' ?>>
                        <option value="">— Sin cargo —</option>
                        <?php foreach ($cargos as $c): ?>
                            <option value="<?= (int) $c['idcargo'] ?>" <?= (string) $v('idcargo') === (string) $c['idcargo'] ? 'selected' : '' ?>>
                                <?= esc($c['nombre']) ?><?= (int) $c['estado'] === 1 ? '' : ' (inactivo)' ?></option>
                        <?php endforeach ?>
                    </select>
                    <div class="invalid-feedback"><?= esc($err['idcargo'] ?? '') ?></div>
                    <?php if ($puedeCargo): ?>
                        <div class="form-text"><a href="<?= site_url('panel/cargos') ?>"><i class="bi bi-gear me-1"></i>Administrar cargos</a></div>
                    <?php else: ?>
                        <div class="form-text">Solo el Administrador asigna cargos.</div>
                    <?php endif ?>
                </div>

                <div class="mb-4">
                    <span class="form-label d-block">Estado</span>
                    <input type="hidden" name="estado" value="0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="estado" name="estado" value="1"
                            <?= (string) $v('estado') === '1' ? 'checked' : '' ?> <?= $yo ? 'disabled' : '' ?>>
                        <label class="form-check-label" for="estado">Cuenta activa</label>
                    </div>
                    <?php if ($yo): ?><input type="hidden" name="estado" value="1"><?php endif ?>
                    <?php if ($id !== null && $u['email_verificado_en'] === null): ?>
                        <div class="form-text text-warning-emphasis"><i class="bi bi-envelope-exclamation"></i>
                            No confirmó su correo. Si activa la cuenta, se dará por verificada.</div>
                    <?php endif ?>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-ig py-2"><i class="bi bi-check2-circle me-1"></i>Guardar</button>
                    <a href="<?= site_url($id === null ? 'panel/usuarios' : "panel/usuarios/{$id}") ?>" class="btn btn-light rounded-pill">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    const form = document.getElementById('formUsuario');
    const pass = document.getElementById('password');
    const conf = document.getElementById('password_confirm');
    document.getElementById('celular').addEventListener('input', function () { this.value = this.value.replace(/\D/g, ''); });
    document.getElementById('generar').addEventListener('click', function () {
        const letras = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ', nums = '23456789';
        const r = n => crypto.getRandomValues(new Uint32Array(1))[0] % n;
        let p = '';
        for (let i = 0; i < 8; i++) p += letras[r(letras.length)];
        for (let i = 0; i < 3; i++) p += nums[r(nums.length)];
        pass.value = conf.value = p;
        document.getElementById('generadaTxt').textContent = p;
        document.getElementById('generadaBox').classList.remove('d-none');
    });
    form.addEventListener('submit', function (e) {
        conf.setCustomValidity(pass.value !== conf.value ? 'No coincide' : '');
        if (pass.value && !/^(?=.*[A-Za-z])(?=.*\d).{8,}$/.test(pass.value)) pass.setCustomValidity('débil'); else pass.setCustomValidity('');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
        form.classList.add('was-validated');
    });
})();
</script>
<?= $this->endSection() ?>
