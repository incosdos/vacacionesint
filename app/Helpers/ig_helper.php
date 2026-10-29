<?php

use Config\Intergraphics;

if (! function_exists('ig_img')) {
    /**
     * Devuelve la URL de una imagen de la empresa.
     * Usa la copia local (public/assets/img/ig/) si existe; si no, la remota.
     */
    function ig_img(string $clave): string
    {
        /** @var Intergraphics $cfg */
        $cfg = config(Intergraphics::class);

        if (! isset($cfg->imagenes[$clave])) {
            return '';
        }

        $ruta  = $cfg->imagenes[$clave];
        $local = 'assets/img/ig/' . basename($ruta);

        if (is_file(FCPATH . $local)) {
            return base_url($local);
        }

        return $cfg->urlRemota . $ruta;
    }
}
