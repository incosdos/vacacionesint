<nav aria-label="breadcrumb">
    <ol class="breadcrumb small mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url('panel') ?>">Panel</a></li>
        <?php if (($miga ?? null) === null): ?>
            <li class="breadcrumb-item active" aria-current="page">Roles y permisos</li>
        <?php else: ?>
            <li class="breadcrumb-item"><a href="<?= site_url('panel/roles') ?>">Roles y permisos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($miga) ?></li>
        <?php endif ?>
    </ol>
</nav>
