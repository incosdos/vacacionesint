<?php

namespace App\Libraries;

use Config\Vacaciones as ConfigVacaciones;
use DateTimeImmutable;

/**
 * Cálculos del módulo de vacaciones: feriados, días hábiles, antigüedad y saldo.
 */
class CalculoVacaciones
{
    private ConfigVacaciones $cfg;

    /** @var array<int, array<string, string>> */
    private array $cacheFeriados = [];

    public function __construct(?ConfigVacaciones $cfg = null)
    {
        $this->cfg = $cfg ?? config(ConfigVacaciones::class);
    }

    public function config(): ConfigVacaciones
    {
        return $this->cfg;
    }

    /** Domingo de Pascua (algoritmo gregoriano anónimo). */
    public static function pascua(int $anio): DateTimeImmutable
    {
        $a = $anio % 19;
        $b = intdiv($anio, 100);
        $c = $anio % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $anio, $mes, $dia));
    }

    /** Tipos de día no laborable. */
    public const TIPOS = [
        'nacional'      => 'Feriado nacional',
        'departamental' => 'Feriado departamental',
        'no_laborable'  => 'Día no laborable',
    ];

    /**
     * Feriados del año registrados por el Administrador (tabla `feriados`).
     * Si el año aún no tiene ninguno registrado, se usan los calculados
     * automáticamente (ver propuestaAnio()).
     *
     * @return array<string, string> 'Y-m-d' => nombre (ordenado)
     */
    public function feriados(int $anio): array
    {
        if (isset($this->cacheFeriados[$anio])) {
            return $this->cacheFeriados[$anio];
        }

        $filas = db_connect()->table('feriados')
            ->select('fecha, nombre')
            ->where('fecha >=', "{$anio}-01-01")
            ->where('fecha <=', "{$anio}-12-31")
            ->orderBy('fecha')
            ->get()->getResultArray();

        $lista = $filas !== []
            ? array_column($filas, 'nombre', 'fecha')
            : array_column($this->propuestaAnio($anio), 'nombre', 'fecha');

        return $this->cacheFeriados[$anio] = $lista;
    }

    /** ¿El año tiene feriados registrados en la base de datos? */
    public function anioRegistrado(int $anio): bool
    {
        return db_connect()->table('feriados')
            ->where('fecha >=', "{$anio}-01-01")->where('fecha <=', "{$anio}-12-31")
            ->countAllResults() > 0;
    }

    /** Limpia la memoria de feriados (después de modificarlos). */
    public function olvidarFeriados(): void
    {
        $this->cacheFeriados = [];
    }

    /**
     * Calendario de feriados de Bolivia calculado (fijos, departamentales de
     * Cochabamba, movibles según la Pascua y ajustes por decreto de app/Config/Vacaciones.php).
     * Se usa para proponer los feriados de un año nuevo.
     *
     * @return list<array{fecha: string, nombre: string, tipo: string}>
     */
    public function propuestaAnio(int $anio): array
    {
        $lista = [];
        foreach ($this->cfg->feriadosFijos as $md => $nombre) {
            $lista["{$anio}-{$md}"] = [$nombre, 'nacional'];
        }
        foreach ($this->cfg->feriadosDepartamentales as $md => $nombre) {
            $lista["{$anio}-{$md}"] = [$nombre, 'departamental'];
        }

        $p = self::pascua($anio);
        $lista[$p->modify('-48 days')->format('Y-m-d')] = ['Carnaval', 'nacional'];
        $lista[$p->modify('-47 days')->format('Y-m-d')] = ['Carnaval', 'nacional'];
        $lista[$p->modify('-2 days')->format('Y-m-d')]  = ['Viernes Santo', 'nacional'];
        $lista[$p->modify('+60 days')->format('Y-m-d')] = ['Corpus Christi', 'nacional'];

        $ajuste = $this->cfg->ajustesPorAnio[$anio] ?? [];
        foreach ($ajuste['quitar'] ?? [] as $f) {
            unset($lista[$f]);
        }
        foreach ($ajuste['agregar'] ?? [] as $f => $nombre) {
            $lista[$f] = [$nombre, 'nacional'];
        }

        ksort($lista);
        $res = [];
        foreach ($lista as $fecha => [$nombre, $tipo]) {
            $res[] = ['fecha' => $fecha, 'nombre' => $nombre, 'tipo' => $tipo];
        }

        return $res;
    }

    public function esHabil(DateTimeImmutable $d): bool
    {
        $dow = (int) $d->format('N'); // 1 = lunes ... 7 = domingo
        if ($dow === 7 || ($dow === 6 && ! $this->cfg->sabadoHabil)) {
            return false;
        }

        return ! isset($this->feriados((int) $d->format('Y'))[$d->format('Y-m-d')]);
    }

    /** Días hábiles entre dos fechas (incluidas). Medio día solo si es un único día hábil. */
    public function diasHabiles(string $inicio, string $fin, bool $medioDia = false): float
    {
        $d   = new DateTimeImmutable($inicio);
        $f   = new DateTimeImmutable($fin);
        $tot = 0;

        for ($i = 0; $d <= $f && $i < 400; $d = $d->modify('+1 day'), $i++) {
            if ($this->esHabil($d)) {
                $tot++;
            }
        }

        return ($medioDia && $tot === 1 && $inicio === $fin) ? 0.5 : (float) $tot;
    }

    /** @return array<string, string> feriados dentro del rango */
    public function feriadosEnRango(string $inicio, string $fin): array
    {
        $res = [];
        for ($a = (int) substr($inicio, 0, 4); $a <= (int) substr($fin, 0, 4); $a++) {
            foreach ($this->feriados($a) as $fecha => $nombre) {
                if ($fecha >= $inicio && $fecha <= $fin) {
                    $res[$fecha] = $nombre;
                }
            }
        }

        return $res;
    }

    /** @return array{anios: int, meses: int}|null */
    public function antiguedad(?string $ingreso, ?string $referencia = null): ?array
    {
        if (! $ingreso) {
            return null;
        }
        $diff = (new DateTimeImmutable($ingreso))->diff(new DateTimeImmutable($referencia ?? 'today'));
        if ($diff->invert) {
            return ['anios' => 0, 'meses' => 0];
        }

        return ['anios' => $diff->y, 'meses' => $diff->m];
    }

    /** Días que corresponden por la antigüedad a una fecha dada. */
    public function diasPorAntiguedad(?string $ingreso, ?string $referencia = null): ?int
    {
        $ant = $this->antiguedad($ingreso, $referencia);
        if ($ant === null) {
            return $this->cfg->diasSinFechaIngreso;
        }

        $escala = $this->cfg->escala;
        krsort($escala);
        foreach ($escala as $anios => $dias) {
            if ($ant['anios'] >= $anios) {
                return $dias;
            }
        }

        return 0;
    }

    /**
     * Saldo de una gestión (año calendario de la fecha de inicio de cada vacación).
     *
     * @return array{gestion: int, asignados: ?int, autorizados: float, pendientes: float, disponibles: ?float}
     */
    public function saldo(array $usuario, int $gestion, ?int $excluirSolicitud = null): array
    {
        $ref = $gestion === (int) date('Y') ? date('Y-m-d') : "{$gestion}-12-31";

        $b = db_connect()->table('vacaciones')
            ->select("COALESCE(SUM(CASE WHEN estado = 'autorizado' THEN dias_habiles END), 0) AS autorizados,
                      COALESCE(SUM(CASE WHEN estado = 'pendiente'  THEN dias_habiles END), 0) AS pendientes", false)
            ->where('idusuario', (int) $usuario['idusuario'])
            ->where('YEAR(fecha_inicio)', $gestion, false);
        if ($excluirSolicitud !== null) {
            $b->where('idvacacion !=', $excluirSolicitud);
        }
        $fila = $b->get()->getRowArray();

        $asignados   = $this->diasPorAntiguedad($usuario['fecha_ingreso'] ?? null, $ref);
        $autorizados = (float) $fila['autorizados'];
        $pendientes  = (float) $fila['pendientes'];

        return [
            'gestion'     => $gestion,
            'asignados'   => $asignados,
            'autorizados' => $autorizados,
            'pendientes'  => $pendientes,
            'disponibles' => $asignados === null ? null : max(0, $asignados - $autorizados - $pendientes),
        ];
    }

    /** Feriados de varios años para el cálculo en el navegador. */
    public function feriadosJson(int $desde, int $hasta): string
    {
        $todo = [];
        for ($a = $desde; $a <= $hasta; $a++) {
            $todo += $this->feriados($a);
        }

        return json_encode($todo, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    }
}
