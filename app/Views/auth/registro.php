<?= $this->extend('layouts/auth') ?>

<?= $this->section('contenido') ?>
<h1 class="h3 fw-bold mb-1">Crear una cuenta</h1>
<p class="text-muted mb-4">Complete sus datos. Le enviaremos un correo para confirmar su registro.</p>

<?php $err = session('errores') ?? []; ?>

<?= form_open('registro', ['novalidate' => true, 'id' => 'formRegistro']) ?>
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control <?= isset($err['nombres']) ? 'is-invalid' : '' ?>" id="nombres" name="nombres"
                   value="<?= esc(old('nombres')) ?>" maxlength="100" autocomplete="given-name" required>
            <div class="invalid-feedback"><?= esc($err['nombres'] ?? 'Ingrese sus nombres.') ?></div>
        </div>
        <div class="col-sm-6">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control <?= isset($err['apellidos']) ? 'is-invalid' : '' ?>" id="apellidos" name="apellidos"
                   value="<?= esc(old('apellidos')) ?>" maxlength="100" autocomplete="family-name" required>
            <div class="invalid-feedback"><?= esc($err['apellidos'] ?? 'Ingrese sus apellidos.') ?></div>
        </div>

        <div class="col-sm-6">
            <label for="ci" class="form-label">Cédula de identidad (CI)</label>
            <input type="text" class="form-control <?= isset($err['ci']) ? 'is-invalid' : '' ?>" id="ci" name="ci"
                   value="<?= esc(old('ci')) ?>" maxlength="20" placeholder="Ej.: 1234567" required>
            <div class="invalid-feedback"><?= esc($err['ci'] ?? 'Ingrese su CI.') ?></div>
        </div>
        <div class="col-sm-6">
            <label for="celular" class="form-label">Celular</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                <input type="tel" inputmode="numeric" pattern="[0-9]{7,15}" class="form-control <?= isset($err['celular']) ? 'is-invalid' : '' ?>"
                       id="celular" name="celular" value="<?= esc(old('celular')) ?>" maxlength="15" placeholder="Ej.: 70012345" required>
                <div class="invalid-feedback"><?= esc($err['celular'] ?? 'Ingrese un número de 7 a 15 dígitos.') ?></div>
            </div>
        </div>

        <div class="col-12">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control <?= isset($err['email']) ? 'is-invalid' : '' ?>" id="email" name="email"
                       value="<?= esc(old('email')) ?>" maxlength="150" autocomplete="email" required>
                <div class="invalid-feedback"><?= esc($err['email'] ?? 'Ingrese un correo válido.') ?></div>
            </div>
        </div>

        <div class="col-sm-6">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group has-validation">
                <input type="password" class="form-control <?= isset($err['password']) ? 'is-invalid' : '' ?>" id="password" name="password"
                       minlength="8" maxlength="72" autocomplete="new-password" required>
                <button class="btn btn-outline-secondary" type="button" data-ver-password="password" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                <div class="invalid-feedback"><?= esc($err['password'] ?? 'Mínimo 8 caracteres, con letras y números.') ?></div>
            </div>
            <div class="form-text">Mínimo 8 caracteres, con letras y números.</div>
        </div>
        <div class="col-sm-6">
            <label for="password_confirm" class="form-label">Confirmar contraseña</label>
            <div class="input-group has-validation">
                <input type="password" class="form-control <?= isset($err['password_confirm']) ? 'is-invalid' : '' ?>" id="password_confirm" name="password_confirm"
                       minlength="8" maxlength="72" autocomplete="new-password" required>
                <button class="btn btn-outline-secondary" type="button" data-ver-password="password_confirm" aria-label="Mostrar contraseña"><i class="bi bi-eye"></i></button>
                <div class="invalid-feedback"><?= esc($err['password_confirm'] ?? 'Las contraseñas no coinciden.') ?></div>
            </div>
        </div>

        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input <?= isset($err['acepto']) ? 'is-invalid' : '' ?>" type="checkbox" id="acepto" name="acepto" value="1"
                       <?= old('acepto') ? 'checked' : '' ?> required>
                <label class="form-check-label small" for="acepto">
                    Acepto que mis datos se usen para la gestión de vacaciones y permisos de <?= esc($empresa->nombreComercial) ?>.
                </label>
            </div>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-ig w-100 py-2">
                <i class="bi bi-person-plus me-1"></i> Registrarme
            </button>
        </div>
    </div>
<?= form_close() ?>

<p class="text-center small mt-4 mb-0">
    ¿Ya tiene una cuenta? <a href="<?= site_url('login') ?>" class="fw-semibold">Iniciar sesión</a>
</p>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Validación en el navegador antes de enviar
(function () {
    const form = document.getElementById('formRegistro');
    const pass = document.getElementById('password');
    const conf = document.getElementById('password_confirm');
    const revisar = () => conf.setCustomValidity(conf.value !== pass.value ? 'No coincide' : '');
    pass.addEventListener('input', revisar);
    conf.addEventListener('input', revisar);
    document.getElementById('celular').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });
    form.addEventListener('submit', function (e) {
        revisar();
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
})();
</script>
<?= $this->endSection() ?>
