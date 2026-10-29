<?= $this->extend('layouts/panel') ?>

<?php
[$estTxt, $estCls, $estIco] = ig_estado_usuario($u);
$this->setVar('migas', ['Panel' => 'panel', 'Usuarios' => 'panel/usuarios', $u['nombres'] . ' ' . $u['apellidos'] => null]);
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
            </div>
        </div>
        <?php if (tiene_permiso('usuarios.gestionar')): ?>
            <div class="d-flex flex-wrap gap-2">
                <?= $this->setData(['compacto' => false])->include('panel/usuarios/_acciones') ?>
            </div>
        <?php endif ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-person-vcard me-1 text-ig-osc"></i>Datos</h2>
                <dl class="row mb-0 ig-dl">
                    <dt class="col-sm-4">Cargo</dt><dd class="col-sm-8"><?= $u['cargo'] ? esc($u['cargo']) : '<span class="text-warning-emphasis">Sin cargo asignado</span>' ?></dd>
                    <dt class="col-sm-4">CI</dt><dd class="col-sm-8"><?= esc($u['ci']) ?></dd>
                    <dt class="col-sm-4">Correo</dt><dd class="col-sm-8 text-break"><a href="mailto:<?= esc($u['email'], 'attr') ?>"><?= esc($u['email']) ?></a></dd>
                    <dt class="col-sm-4">Celular</dt><dd class="col-sm-8"><?= esc((string) $u['celular']) ?></dd>
                    <dt class="col-sm-4">Fecha de ingreso</dt>
                    <dd class="col-sm-8"><?php if ($u['fecha_ingreso']): ?><?= ig_fecha($u['fecha_ingreso']) ?>
                        <span class="text-muted small">(<?= $antig['anios'] ?> año<?= $antig['anios'] === 1 ? '' : 's' ?><?= $antig['meses'] ? ', ' . $antig['meses'] . ' mes' . ($antig['meses'] === 1 ? '' : 'es') : '' ?>)</span>
                        <?php else: ?><span class="text-warning-emphasis">Sin registrar</span><?php endif ?></dd>
                    <dt class="col-sm-4">Fecha de registro</dt><dd class="col-sm-8"><?= ig_fecha($u['fechaderegistro'], true) ?></dd>
                    <dt class="col-sm-4">Correo verificado</dt><dd class="col-sm-8"><?= $u['email_verificado_en'] ? ig_fecha($u['email_verificado_en'], true) : '<span class="text-warning-emphasis">No</span>' ?></dd>
                    <dt class="col-sm-4">Último acceso</dt><dd class="col-sm-8"><?= ig_fecha($u['ultimo_acceso'], true) ?></dd>
                    <dt class="col-sm-4">Última modificación</dt><dd class="col-sm-8"><?= ig_fecha($u['updated_at'], true) ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 fw-semibold mb-3"><i class="bi bi-calendar2-week me-1 text-ig-osc"></i>Vacaciones · gestión <?= (int) $saldo['gestion'] ?></h2>
                <p class="small mb-3">
                    <?php if ($saldo['asignados'] === null): ?>
                        <span class="text-warning-emphasis"><i class="bi bi-info-circle"></i> Registre la fecha de ingreso para calcular el saldo.</span>
                    <?php else: ?>
                        Le corresponden <strong><?= vac_dias($saldo['asignados']) ?></strong> ·
                        disponibles <strong class="text-ig-osc"><?= vac_dias($saldo['disponibles']) ?></strong>
                    <?php endif ?>
                </p>
                <div class="row g-3 text-center">
                    <div class="col-6"><div class="ig-mini"><span><?= (int) ($vac['total'] ?? 0) ?></span>Solicitudes</div></div>
                    <div class="col-6"><div class="ig-mini"><span><?= (int) ($vac['pendientes'] ?? 0) ?></span>Pendientes</div></div>
                    <div class="col-6"><div class="ig-mini"><span><?= (int) ($vac['autorizadas'] ?? 0) ?></span>Autorizadas</div></div>
                    <div class="col-6"><div class="ig-mini"><span><?= rtrim(rtrim(number_format((float) ($vac['dias'] ?? 0), 1, ',', ''), '0'), ',') ?></span>Días autorizados</div></div>
                </div>
                <?php if (tiene_permiso('vacaciones.ver_todas') || tiene_permiso('vacaciones.autorizar')): ?>
                    <a class="btn btn-sm btn-outline-dark rounded-pill mt-3" href="<?= site_url('panel/solicitudes?vista=historial&q=' . urlencode($u['ci'])) ?>">
                        <i class="bi bi-list-ul me-1"></i>Ver su historial</a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->include('panel/usuarios/_modales') ?>
<?= $this->endSection() ?>
