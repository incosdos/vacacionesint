<?php

if (! function_exists('usuario_actual')) {
    /** Datos del usuario en sesión (o null). */
    function usuario_actual(): ?array
    {
        if (! session('idusuario')) {
            return null;
        }

        return [
            'idusuario' => session('idusuario'),
            'nombre'    => session('nombre'),
            'email'     => session('email'),
            'idrol'     => session('idrol'),
            'rol'       => session('rol'),
        ];
    }
}

if (! function_exists('tiene_permiso')) {
    function tiene_permiso(string $clave): bool
    {
        return in_array($clave, session('permisos') ?? [], true);
    }
}

if (! function_exists('ig_pendientes_autorizar')) {
    /** Solicitudes pendientes que el usuario actual puede resolver (0 si no autoriza). */
    function ig_pendientes_autorizar(): int
    {
        static $n = null;
        if ($n === null) {
            $n = tiene_permiso('vacaciones.autorizar')
                ? model(\App\Models\VacacionModel::class)->pendientesPara((int) session('idusuario'))
                : 0;
        }

        return $n;
    }
}
