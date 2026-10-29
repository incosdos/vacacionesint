<?php
/* Título de la pestaña: siempre empieza con la marca, p. ej. «intergraphics · Panel». */
$t = trim(str_ireplace(['| INTERGRAPHICS', 'INTERGRAPHICS |', 'INTERGRAPHICS'], '', (string) ($titulo ?? '')), " |·-");
?>
<title>intergraphics<?= $t !== '' ? ' · ' . esc($t) : '' ?></title>
