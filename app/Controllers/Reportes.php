<?php

namespace App\Controllers;

use App\Libraries\ReporteVacaciones;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Intergraphics;

/**
 * Reportes (permiso reportes.ver): resumen de vacaciones del personal.
 */
class Reportes extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'usuarios', 'vacaciones'];

    private function filtros(ReporteVacaciones $rep): array
    {
        $gestiones = $rep->gestiones();
        $gestion   = (int) $this->request->getGet('gestion');

        return [
            'gestion'   => in_array($gestion, $gestiones, true) ? $gestion : (int) date('Y'),
            'q'         => trim((string) $this->request->getGet('q')),
            'rol'       => (string) $this->request->getGet('rol'),
            'cargo'     => (string) $this->request->getGet('cargo'),
            'inactivos' => $this->request->getGet('inactivos') === '1',
        ];
    }

    public function index(): string
    {
        $rep     = new ReporteVacaciones();
        $f       = $this->filtros($rep);
        $resumen = $rep->resumen($f['gestion'], $f);
        $ids     = array_map('intval', array_column($resumen['filas'], 'idusuario'));

        return view('panel/reportes/index', [
            'titulo'    => 'Reportes | INTERGRAPHICS',
            'empresa'   => config(Intergraphics::class),
            'seccion'   => 'reportes',
            'f'         => $f,
            'gestiones' => $rep->gestiones(),
            'roles'     => $rep->rolesConVacaciones(),
            'cargos'    => model(\App\Models\CargoModel::class)->orderBy('nombre')->findAll(),
            'filas'     => $resumen['filas'],
            't'         => $resumen['totales'],
            'meses'     => $rep->diasPorMes($f['gestion'], $ids ?: [0]),
            'proximas'  => $f['gestion'] === (int) date('Y') ? $rep->proximasAusencias(30, $ids ?: [0]) : [],
        ]);
    }

    /** Exporta el resumen a CSV (se abre directamente en Excel). */
    public function exportar(): ResponseInterface
    {
        $rep     = new ReporteVacaciones();
        $f       = $this->filtros($rep);
        $resumen = $rep->resumen($f['gestion'], $f);

        $num = static fn ($n) => $n === null ? '' : str_replace('.', ',', rtrim(rtrim(number_format((float) $n, 1, '.', ''), '0'), '.'));

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF"); // BOM: Excel reconoce UTF-8 (tildes y ñ)
        fputcsv($out, ['Resumen de vacaciones del personal - Gestión ' . $f['gestion']], ';');
        fputcsv($out, ['International Graphics Outsourcing S.R.L. - Generado el ' . date('d/m/Y H:i') . ' por ' . session('nombre')], ';');
        fputcsv($out, [], ';');
        fputcsv($out, ['Apellidos', 'Nombres', 'CI', 'Cargo', 'Rol', 'Estado', 'Fecha de ingreso', 'Antigüedad (años)',
            'Días que corresponden', 'Días autorizados', 'Días pendientes', 'Días disponibles', '% de uso',
            'Solicitudes autorizadas', 'Solicitudes pendientes', 'Solicitudes rechazadas'], ';');

        foreach ($resumen['filas'] as $r) {
            fputcsv($out, [
                $r['apellidos'], $r['nombres'], $r['ci'], $r['cargo'] ?? 'Sin cargo', $r['rol'], (int) $r['estado'] === 1 ? 'Activo' : 'Inactivo',
                $r['fecha_ingreso'] ? date('d/m/Y', strtotime($r['fecha_ingreso'])) : 'Sin registrar',
                $r['antiguedad']['anios'] ?? '',
                $r['asignados'] ?? 'Sin dato', $num($r['autorizados']), $num($r['pendientes']), $num($r['disponibles']),
                $r['uso'] === null ? '' : $r['uso'] . '%',
                (int) $r['n_autorizadas'], (int) $r['n_pendientes'], (int) $r['n_rechazadas'],
            ], ';');
        }
        $t = $resumen['totales'];
        fputcsv($out, ['TOTAL', $t['personas'] . ' personas', '', '', '', '', '', '', $t['asignados'], $num($t['autorizados']),
            $num($t['pendientes']), $num($t['disponibles']), $t['uso'] === null ? '' : $t['uso'] . '%',
            $t['solicitudes_aut'], $t['solicitudes_pend'], $t['solicitudes_rech']], ';');

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="resumen_vacaciones_' . $f['gestion'] . '.csv"')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody($csv);
    }
}
