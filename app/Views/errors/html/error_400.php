<?php
$codigo  = 400;
$titulo  = 'Solicitud no válida';
$texto   = 'No pudimos procesar su solicitud. Si estaba llenando un formulario, recargue la página e intente de nuevo.';
$icono   = 'bi-exclamation-triangle';
$detalle = $message ?? '';
include __DIR__ . '/_plantilla.php';
