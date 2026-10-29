<?php /** Tarjetas de saldo. Requiere $saldo. */ ?>
<div class="row g-3">
    <div class="col-6 col-lg-3">
        <div class="ig-saldo"><span class="ig-saldo-num"><?= $saldo['asignados'] === null ? '—' : (int) $saldo['asignados'] ?></span>
            <span class="ig-saldo-txt">Días que le corresponden</span></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ig-saldo"><span class="ig-saldo-num"><?= rtrim(rtrim(number_format($saldo['autorizados'], 1, ',', ''), '0'), ',') ?></span>
            <span class="ig-saldo-txt">Autorizados</span></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ig-saldo"><span class="ig-saldo-num"><?= rtrim(rtrim(number_format($saldo['pendientes'], 1, ',', ''), '0'), ',') ?></span>
            <span class="ig-saldo-txt">En revisión</span></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ig-saldo ig-saldo-destacado"><span class="ig-saldo-num"><?= $saldo['disponibles'] === null ? '—' : rtrim(rtrim(number_format($saldo['disponibles'], 1, ',', ''), '0'), ',') ?></span>
            <span class="ig-saldo-txt">Disponibles</span></div>
    </div>
</div>
