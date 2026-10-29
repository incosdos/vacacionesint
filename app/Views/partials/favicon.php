<?php
/* Ícono del colibrí en la pestaña. ?v= cambia cuando cambia el archivo y obliga al navegador a renovarlo. */
$icono_v = static fn (string $f) => base_url($f) . '?v=' . (is_file(FCPATH . $f) ? filemtime(FCPATH . $f) : '1');
?>
<link rel="icon" href="<?= $icono_v('favicon.ico') ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?= $icono_v('assets/img/marca/colibri.png') ?>">
    <link rel="apple-touch-icon" href="<?= $icono_v('assets/img/marca/apple-touch-icon.png') ?>">
    <meta name="theme-color" content="#00818e">
