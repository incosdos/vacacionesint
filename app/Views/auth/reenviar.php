<?= $this->extend('layouts/auth') ?>

<?= $this->section('contenido') ?>
<h1 class="h3 fw-bold mb-1">Reenviar confirmación</h1>
<p class="text-muted mb-4">Ingrese el correo con el que se registró y le enviaremos un nuevo enlace de confirmación.</p>

<?= form_open('registro/reenviar') ?>
    <div class="mb-4">
        <label for="email" class="form-label">Correo electrónico</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>"
                   autocomplete="email" required autofocus>
        </div>
    </div>
    <button type="submit" class="btn btn-ig w-100 py-2"><i class="bi bi-send me-1"></i> Reenviar enlace</button>
<?= form_close() ?>

<p class="text-center small mt-4 mb-0">
    <a href="<?= site_url('login') ?>" class="fw-semibold">Volver a iniciar sesión</a>
</p>
<?= $this->endSection() ?>
