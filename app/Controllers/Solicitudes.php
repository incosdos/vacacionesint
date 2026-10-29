<?php

namespace App\Controllers;

use App\Libraries\CalculoVacaciones;
use App\Libraries\Correo;
use App\Models\UsuarioModel;
use App\Models\VacacionModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * Solicitudes del personal (vacaciones.ver_todas / vacaciones.autorizar).
 */
class Solicitudes extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'vacaciones'];

    public function index(): string
    {
        $vista   = $this->request->getGet('vista') === 'historial' ? 'historial' : 'pendientes';
        $filtros = [
            'q'      => trim((string) $this->request->getGet('q')),
            'estado' => (string) $this->request->getGet('estado'),
            'desde'  => (string) $this->request->getGet('desde'),
            'hasta'  => (string) $this->request->getGet('hasta'),
        ];
        $yo     = (int) session('idusuario');
        $modelo = model(VacacionModel::class);
        $calc   = new CalculoVacaciones();
        $datos  = [
            'titulo'        => 'Solicitudes del personal | INTERGRAPHICS',
            'empresa'       => config(Intergraphics::class),
            'seccion'       => 'solicitudes',
            'vista'         => $vista,
            'filtros'       => $filtros,
            'totalPend'     => model(VacacionModel::class)->where('estado', 'pendiente')->countAllResults(),
            'puedeAutorizar'=> tiene_permiso('vacaciones.autorizar'),
        ];

        if ($vista === 'pendientes') {
            $pendientes = $modelo->filtrar(['estado' => 'pendiente'] + $filtros)->findAll();
            foreach ($pendientes as &$p) {
                $p['saldo']    = $calc->saldo(['idusuario' => $p['idusuario'], 'fecha_ingreso' => $p['fecha_ingreso']],
                    (int) substr($p['fecha_inicio'], 0, 4), (int) $p['idvacacion']);
                $p['ausentes'] = model(VacacionModel::class)->ausentesEn($p['fecha_inicio'], $p['fecha_fin'], (int) $p['idusuario']);
                $p['propia']   = (int) $p['idusuario'] === $yo;
                $p['feriados'] = $calc->feriadosEnRango($p['fecha_inicio'], $p['fecha_fin']);
            }
            unset($p);
            $datos['pendientes'] = $pendientes;
        } else {
            $datos['solicitudes'] = $modelo->filtrar($filtros)->paginate(15);
            $datos['pager']       = $modelo->pager;
        }

        return view('panel/solicitudes/index', $datos);
    }

    public function resolver(int $id): RedirectResponse
    {
        $accion      = (string) $this->request->getPost('accion');
        $observacion = trim((string) $this->request->getPost('observacion'));
        $modelo      = model(VacacionModel::class);
        $sol         = $modelo->detalle($id);
        $volver      = redirect()->to(site_url('panel/solicitudes'));

        if ($sol === null || ! in_array($accion, ['autorizar', 'rechazar'], true)) {
            return $volver->with('error', 'Solicitud no válida.');
        }
        if ((int) $sol['idusuario'] === (int) session('idusuario')) {
            return $volver->with('error', 'No puede autorizar ni rechazar su propia solicitud. Debe hacerlo otro coordinador o el administrador.');
        }
        if ($accion === 'rechazar' && mb_strlen($observacion) < 5) {
            return $volver->with('error', 'Para rechazar una solicitud indique el motivo (al menos 5 caracteres).');
        }
        if (mb_strlen($observacion) > 255) {
            return $volver->with('error', 'La observación no puede superar 255 caracteres.');
        }

        $estado = $accion === 'autorizar' ? 'autorizado' : 'rechazado';
        if (! $modelo->resolver($id, $estado, (int) session('idusuario'), $observacion ?: null)) {
            return $volver->with('error', 'Esta solicitud ya fue resuelta o cancelada por otra persona.');
        }

        // Aviso al solicitante
        $lineas = [
            'Periodo: ' . vac_periodo($sol['fecha_inicio'], $sol['fecha_fin']),
            'Días hábiles: ' . vac_dias($sol['dias_habiles']),
            'Resuelto por: ' . session('nombre'),
        ];
        if ($observacion !== '') {
            $lineas[] = 'Observación: ' . $observacion;
        }
        $nombres = explode(' ', $sol['solicitante'])[0];
        Correo::notificar(
            $sol['email_solicitante'],
            $nombres,
            $estado === 'autorizado' ? 'Sus vacaciones fueron autorizadas' : 'Su solicitud de vacaciones fue rechazada',
            $estado === 'autorizado' ? '¡Sus vacaciones fueron autorizadas!' : 'Su solicitud de vacaciones fue rechazada',
            $lineas,
            'Ver mis vacaciones',
            site_url('panel/vacaciones'),
        );

        return $volver->with('exito', ($estado === 'autorizado' ? 'Vacaciones autorizadas' : 'Solicitud rechazada')
            . " para {$sol['solicitante']} (" . vac_periodo($sol['fecha_inicio'], $sol['fecha_fin']) . ').');
    }
}
