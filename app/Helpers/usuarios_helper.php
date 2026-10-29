<?php

// Funciones de presentación del módulo de usuarios
if (! function_exists('ig_estado_usuario')) {
    /** @return array{0: string, 1: string, 2: string} [texto, clase, icono] */
    function ig_estado_usuario(array $u): array
    {
        if ($u['email_verificado_en'] === null) {
            return ['Pendiente de verificación', 'ig-estado-pendiente', 'bi-envelope-exclamation'];
        }

        return (int) $u['estado'] === 1
            ? ['Activo', 'ig-estado-activo', 'bi-check-circle']
            : ['Inactivo', 'ig-estado-inactivo', 'bi-slash-circle'];
    }

    function ig_iniciales(array $u): string
    {
        return mb_strtoupper(mb_substr($u['nombres'], 0, 1) . mb_substr($u['apellidos'], 0, 1));
    }

    function ig_fecha(?string $f, bool $hora = false): string
    {
        if (! $f) {
            return '—';
        }
        $t = strtotime($f);

        return date($hora ? 'd/m/Y H:i' : 'd/m/Y', $t);
    }
}
