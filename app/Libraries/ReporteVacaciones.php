<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use DateTimeImmutable;

/**
 * Datos del reporte "Resumen de vacaciones del personal".
 */
class ReporteVacaciones
{
    private BaseConnection $db;
    private CalculoVacaciones $calc;

    public function __construct(?CalculoVacaciones $calc = null)
    {
        $this->db   = db_connect();
        $this->calc = $calc ?? new CalculoVacaciones();
    }

    /** Roles que solicitan vacaciones (tienen el permiso vacaciones.solicitar). */
    public function rolesConVacaciones(): array
    {
        return $this->db->table('roles r')
            ->select('r.idrol, r.nombre')
            ->join('rol_permiso rp', 'rp.idrol = r.idrol')
            ->join('permisos p', 'p.idpermiso = rp.idpermiso')
            ->where('p.clave', 'vacaciones.solicitar')
            ->orderBy('r.idrol')
            ->get()->getResultArray();
    }

    /** Años con datos (para el selector de gestión). */
    public function gestiones(): array
    {
        $fila = $this->db->table('vacaciones')->select('MIN(YEAR(fecha_inicio)) AS desde, MAX(YEAR(fecha_inicio)) AS hasta', false)->get()->getRowArray();
        $actual = (int) date('Y');
        $desde  = min((int) ($fila['desde'] ?? $actual) ?: $actual, $actual);
        $hasta  = max((int) ($fila['hasta'] ?? $actual) ?: $actual, $actual);

        return range($hasta, $desde);
    }

    /**
     * Resumen por persona y totales.
     *
     * @param array{q?: string, rol?: string, cargo?: string, inactivos?: bool} $f
     */
    public function resumen(int $gestion, array $f = []): array
    {
        $roles = array_column($this->rolesConVacaciones(), 'idrol');
        if ($roles === []) {
            return ['filas' => [], 'totales' => $this->totalesVacios()];
        }

        $hoy = date('Y-m-d');
        $b = $this->db->table('usuarios u')
            ->select("u.idusuario, u.nombres, u.apellidos, u.ci, u.fecha_ingreso, u.estado, r.nombre AS rol, u.idrol, c.nombre AS cargo,
                COALESCE(SUM(CASE WHEN v.estado = 'autorizado' THEN v.dias_habiles END), 0) AS autorizados,
                COALESCE(SUM(CASE WHEN v.estado = 'pendiente'  THEN v.dias_habiles END), 0) AS pendientes,
                SUM(v.estado = 'autorizado') AS n_autorizadas,
                SUM(v.estado = 'pendiente')  AS n_pendientes,
                SUM(v.estado = 'rechazado')  AS n_rechazadas,
                MAX(CASE WHEN v.estado = 'autorizado' AND v.fecha_inicio <= " . $this->db->escape($hoy) . " THEN v.fecha_fin END) AS ultima,
                MAX(CASE WHEN v.estado = 'autorizado' AND " . $this->db->escape($hoy) . ' BETWEEN v.fecha_inicio AND v.fecha_fin THEN 1 ELSE 0 END) AS de_vacaciones', false)
            ->join('roles r', 'r.idrol = u.idrol')
            ->join('cargos c', 'c.idcargo = u.idcargo', 'left')
            ->join('vacaciones v', 'v.idusuario = u.idusuario AND YEAR(v.fecha_inicio) = ' . (int) $gestion, 'left', false)
            ->where('u.email_verificado_en IS NOT NULL')
            ->whereIn('u.idrol', $roles)
            ->groupBy('u.idusuario')
            ->orderBy('u.apellidos')->orderBy('u.nombres');

        if (empty($f['inactivos'])) {
            $b->where('u.estado', 1);
        }
        if (! empty($f['rol'])) {
            $b->where('u.idrol', (int) $f['rol']);
        }
        if (($f['cargo'] ?? '') === 'sin') {
            $b->where('u.idcargo', null);
        } elseif (! empty($f['cargo'])) {
            $b->where('u.idcargo', (int) $f['cargo']);
        }
        if (($q = trim((string) ($f['q'] ?? ''))) !== '') {
            $b->groupStart()->like("CONCAT(u.nombres, ' ', u.apellidos)", $q, 'both', null, true)->orLike('u.ci', $q)->groupEnd();
        }

        $ref   = $gestion === (int) date('Y') ? $hoy : "{$gestion}-12-31";
        $filas = [];
        $t     = $this->totalesVacios();

        foreach ($b->get()->getResultArray() as $r) {
            $asignados   = $this->calc->diasPorAntiguedad($r['fecha_ingreso'], $ref);
            $autorizados = (float) $r['autorizados'];
            $pendientes  = (float) $r['pendientes'];
            $antig       = $this->calc->antiguedad($r['fecha_ingreso'], $ref);

            $r['asignados']   = $asignados;
            $r['autorizados'] = $autorizados;
            $r['pendientes']  = $pendientes;
            $r['disponibles'] = $asignados === null ? null : max(0, $asignados - $autorizados - $pendientes);
            $r['uso']         = $asignados ? min(100, round($autorizados * 100 / $asignados)) : null;
            $r['antiguedad']  = $antig;
            $filas[]          = $r;

            $t['personas']++;
            $t['autorizados'] += $autorizados;
            $t['pendientes']  += $pendientes;
            $t['solicitudes_aut'] += (int) $r['n_autorizadas'];
            $t['solicitudes_pend'] += (int) $r['n_pendientes'];
            $t['solicitudes_rech'] += (int) $r['n_rechazadas'];
            $t['de_vacaciones'] += (int) $r['de_vacaciones'];
            if ($asignados !== null) {
                $t['asignados']   += $asignados;
                $t['disponibles'] += $r['disponibles'];
                $t['con_saldo']++;
            } else {
                $t['sin_fecha']++;
            }
            if ($autorizados == 0 && $asignados) {
                $t['sin_tomar']++;
            }
        }

        $t['uso'] = $t['asignados'] > 0 ? round($t['autorizados'] * 100 / $t['asignados']) : null;

        return ['filas' => $filas, 'totales' => $t];
    }

    private function totalesVacios(): array
    {
        return ['personas' => 0, 'asignados' => 0, 'autorizados' => 0.0, 'pendientes' => 0.0, 'disponibles' => 0.0,
            'solicitudes_aut' => 0, 'solicitudes_pend' => 0, 'solicitudes_rech' => 0, 'de_vacaciones' => 0,
            'sin_fecha' => 0, 'sin_tomar' => 0, 'con_saldo' => 0, 'uso' => null];
    }

    /**
     * Días hábiles autorizados por mes de la gestión.
     *
     * @return array<int, float> 1..12
     */
    public function diasPorMes(int $gestion, array $idsUsuarios = []): array
    {
        $meses = array_fill(1, 12, 0.0);
        $b = $this->db->table('vacaciones')
            ->select('fecha_inicio, fecha_fin, dias_habiles')
            ->where('estado', 'autorizado')
            ->where('YEAR(fecha_inicio)', $gestion, false);
        if ($idsUsuarios !== []) {
            $b->whereIn('idusuario', $idsUsuarios);
        }

        foreach ($b->get()->getResultArray() as $v) {
            if ((float) $v['dias_habiles'] === 0.5) {
                $meses[(int) substr($v['fecha_inicio'], 5, 2)] += 0.5;
                continue;
            }
            $d   = new DateTimeImmutable($v['fecha_inicio']);
            $fin = new DateTimeImmutable($v['fecha_fin']);
            for (; $d <= $fin; $d = $d->modify('+1 day')) {
                if ((int) $d->format('Y') === $gestion && $this->calc->esHabil($d)) {
                    $meses[(int) $d->format('n')]++;
                }
            }
        }

        return $meses;
    }

    /** Vacaciones autorizadas en curso o que empiezan en los próximos N días. */
    public function proximasAusencias(int $dias = 30, array $idsUsuarios = []): array
    {
        $hoy   = date('Y-m-d');
        $hasta = date('Y-m-d', strtotime("+{$dias} days"));
        $b = $this->db->table('vacaciones v')
            ->select("v.fecha_inicio, v.fecha_fin, v.dias_habiles, CONCAT(u.nombres, ' ', u.apellidos) AS nombre, r.nombre AS rol", false)
            ->join('usuarios u', 'u.idusuario = v.idusuario')
            ->join('roles r', 'r.idrol = u.idrol')
            ->where('v.estado', 'autorizado')
            ->where('v.fecha_fin >=', $hoy)
            ->where('v.fecha_inicio <=', $hasta)
            ->orderBy('v.fecha_inicio');
        if ($idsUsuarios !== []) {
            $b->whereIn('v.idusuario', $idsUsuarios);
        }

        return $b->get()->getResultArray();
    }
}
