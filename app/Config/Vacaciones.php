<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Reglas del módulo de vacaciones.
 */
class Vacaciones extends BaseConfig
{
    /**
     * Días hábiles de vacación por gestión según antigüedad
     * (Ley General del Trabajo de Bolivia, art. 44): años cumplidos => días.
     * Con menos de 1 año de antigüedad no corresponden vacaciones.
     */
    public array $escala = [
        10 => 30, // 10 años o más
        5  => 20, // de 5 a 10 años
        1  => 15, // de 1 a 5 años
    ];

    /** Si el usuario no tiene fecha de ingreso, no se controla el saldo (null). */
    public ?int $diasSinFechaIngreso = null;

    /** Impedir solicitudes que superen el saldo disponible. */
    public bool $bloquearSinSaldo = true;

    /** Días de anticipación mínima para solicitar (0 = puede empezar hoy). */
    public int $diasAnticipacion = 1;

    /** Máximo de días hábiles por solicitud. */
    public int $maxDiasPorSolicitud = 30;

    /** Considerar el sábado como día hábil (false = lunes a viernes). */
    public bool $sabadoHabil = false;

    /*
     * IMPORTANTE: desde la actualización 03 los feriados se administran en la
     * base de datos (tabla `feriados`, menú Administración → Feriados).
     * Las listas siguientes solo se usan para PROPONER los feriados de un año
     * nuevo con el botón «Generar feriados del año».
     */

    /** Feriados nacionales de fecha fija (mes-día => nombre). */
    public array $feriadosFijos = [
        '01-01' => 'Año Nuevo',
        '01-22' => 'Día del Estado Plurinacional',
        '05-01' => 'Día del Trabajo',
        '06-21' => 'Año Nuevo Andino Amazónico',
        '08-06' => 'Día de la Independencia',
        '11-02' => 'Día de Todos los Difuntos',
        '12-25' => 'Navidad',
    ];

    /** Feriados departamentales (Cochabamba). */
    public array $feriadosDepartamentales = [
        '09-14' => 'Aniversario de Cochabamba',
    ];

    /**
     * Ajustes por año según decretos (traslados y feriados adicionales).
     * 2026: Decreto Supremo N° 5521.
     */
    public array $ajustesPorAnio = [
        2026 => [
            'quitar'  => ['2026-01-22', '2026-06-21'],
            'agregar' => [
                '2026-01-23' => 'Día del Estado Plurinacional (trasladado)',
                '2026-06-05' => 'Corpus Christi (feriado adicional)',
                '2026-06-22' => 'Año Nuevo Andino Amazónico (trasladado)',
                '2026-08-07' => 'Día de la Independencia (feriado adicional)',
            ],
        ],
    ];
}
