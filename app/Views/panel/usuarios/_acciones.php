<?php
/** Botones de acción de un usuario. Requiere $u; $compacto = true para menú desplegable. */
$id      = (int) $u['idusuario'];
$nombre  = $u['nombres'] . ' ' . $u['apellidos'];
$yo      = $id === (int) session('idusuario');
$activo  = (int) $u['estado'] === 1 && $u['email_verificado_en'] !== null;
$pend    = $u['email_verificado_en'] === null;
$clase   = $compacto ? 'dropdown-item' : 'btn btn-sm btn-outline-secondary rounded-pill';
?>
<?php if ($compacto): ?>
    <a class="<?= $clase ?>" href="<?= site_url("panel/usuarios/{$id}") ?>"><i class="bi bi-eye me-2"></i>Ver detalle</a>
<?php endif ?>
<?php if (tiene_permiso('usuarios.gestionar')): ?>
    <a class="<?= $clase ?>" href="<?= site_url("panel/usuarios/{$id}/editar") ?>"><i class="bi bi-pencil-square me-2"></i>Editar</a>
    <?php if (! $yo): ?>
        <button type="button" class="<?= $clase ?>" data-bs-toggle="modal" data-bs-target="#modalRol"
                data-url="<?= site_url("panel/usuarios/{$id}/rol") ?>" data-nombre="<?= esc($nombre, 'attr') ?>" data-rol-actual="<?= (int) $u['idrol'] ?>">
            <i class="bi bi-person-badge me-2"></i>Asignar rol
        </button>
        <?php if ($pend): ?>
            <button type="button" class="<?= $clase ?>" data-bs-toggle="modal" data-bs-target="#modalConfirmar"
                    data-url="<?= site_url("panel/usuarios/{$id}/reenviar") ?>" data-titulo="Reenviar verificación"
                    data-texto="<?= esc("Se enviará un nuevo enlace de confirmación a {$u['email']}.", 'attr') ?>" data-boton="Reenviar">
                <i class="bi bi-envelope-arrow-up me-2"></i>Reenviar verificación
            </button>
        <?php endif ?>
        <button type="button" class="<?= $clase ?> <?= $activo ? 'text-warning-emphasis' : 'text-success' ?>" data-bs-toggle="modal" data-bs-target="#modalConfirmar"
                data-url="<?= site_url("panel/usuarios/{$id}/estado") ?>"
                data-titulo="<?= $activo ? 'Desactivar cuenta' : 'Activar cuenta' ?>"
                data-texto="<?= esc($activo
                    ? "{$nombre} ya no podrá ingresar al sistema. Si tiene la sesión abierta, se cerrará en su siguiente acción."
                    : ($pend ? "{$nombre} aún no confirmó su correo. Al activarla, la cuenta se dará por verificada." : "{$nombre} podrá volver a ingresar al sistema."), 'attr') ?>"
                data-boton="<?= $activo ? 'Desactivar' : 'Activar' ?>" data-estilo="<?= $activo ? 'btn-warning' : 'btn-success' ?>">
            <i class="bi <?= $activo ? 'bi-toggle-off' : 'bi-toggle-on' ?> me-2"></i><?= $activo ? 'Desactivar' : 'Activar' ?>
        </button>
        <?php if ($compacto): ?><hr class="dropdown-divider"><?php endif ?>
        <button type="button" class="<?= $clase ?> text-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmar"
                data-url="<?= site_url("panel/usuarios/{$id}/eliminar") ?>" data-titulo="Eliminar usuario"
                data-texto="<?= esc("¿Eliminar definitivamente a {$nombre}? Si tiene vacaciones registradas no se podrá eliminar; en ese caso desactívelo.", 'attr') ?>"
                data-boton="Eliminar" data-estilo="btn-danger">
            <i class="bi bi-trash me-2"></i>Eliminar
        </button>
    <?php endif ?>
<?php endif ?>
