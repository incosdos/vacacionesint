<?= $this->extend('layouts/auth') ?>

<?= $this->section('contenido') ?>
<h1 class="h3 fw-bold mb-1">Iniciar sesión</h1>
<p class="text-muted mb-4">Ingrese con su correo o CI y su contraseña.</p>

<?= form_open('login') ?>
    <div class="mb-3">
        <label for="usuario" class="form-label">Correo o CI</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="usuario" name="usuario"
                   value="<?= esc(old('usuario')) ?>" autocomplete="username" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password"
                   autocomplete="current-password" required>
            <button class="btn btn-outline-secondary" type="button" data-ver-password="password" aria-label="Mostrar contraseña">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="text-end mb-4">
        <a href="<?= site_url('registro/reenviar') ?>" class="small">¿No recibió el correo?</a>
    </div>

    <button type="submit" class="btn btn-ig w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
    </button>
<?= form_close() ?>

<div class="ig-separador"><span>¿Es nuevo?</span></div>

<a href="<?= site_url('registro') ?>" class="btn btn-outline-dark w-100 py-2 rounded-pill">
    <i class="bi bi-person-plus me-1"></i> Crear una cuenta
</a>
<?= $this->endSection() ?>
