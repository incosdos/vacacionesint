<?php
$codigo  = 404;
$titulo  = 'Página no encontrada';
$texto   = 'La página que busca no existe o fue movida. Revise la dirección o vuelva al inicio.';
$icono   = 'bi-signpost-split';
$detalle = $message ?? '';
include __DIR__ . '/_plantilla.php';
