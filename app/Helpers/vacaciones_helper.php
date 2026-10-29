<?php

// Presentación del módulo de vacaciones

if (! function_exists('vac_estado')) {
    /** @return array{0: string, 1: string, 2: string} [texto, clase, icono] */
    function vac_estado(string $estado): array
    {
        return match ($estado) {
            'pendiente'  => ['Pendiente', 'ig-vac-pendiente', 'bi-hourglass-split'],
            'autorizado' => ['Autorizada', 'ig-vac-autorizado', 'bi-check-circle'],
            'rechazado'  => ['Rechazada', 'ig-vac-rechazado', 'bi-x-circle'],
            'cancelado'  => ['Cancelada', 'ig-vac-cancelado', 'bi-slash-circle'],
            default      => [ucfirst($estado), 'ig-vac-cancelado', 'bi-circle'],
        };
    }

    function vac_fecha(?string $f, string $formato = 'EEE d MMM y'): string
    {
        if (! $f) {
            return '—';
        }
        if (class_exists(IntlDateFormatter::class)) {
            $fmt = new IntlDateFormatter('es_BO', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'America/La_Paz', null, $formato);

            return rtrim((string) $fmt->format(new DateTime($f)), '.');
        }

        return date('d/m/Y', strtotime($f));
    }

    /** "lun 3 nov – vie 7 nov 2026" */
    function vac_periodo(string $inicio, string $fin): string
    {
        if ($inicio === $fin) {
            return vac_fecha($inicio);
        }
        $mismoAnio = substr($inicio, 0, 4) === substr($fin, 0, 4);

        return vac_fecha($inicio, $mismoAnio ? 'EEE d MMM' : 'EEE d MMM y') . ' – ' . vac_fecha($fin);
    }

    function vac_dias(float|string|null $dias): string
    {
        $d = (float) $dias;
        $txt = rtrim(rtrim(number_format($d, 1, ',', ''), '0'), ',');

        return $txt . ' ' . ($d == 1 ? 'día' : 'días');
    }
}
