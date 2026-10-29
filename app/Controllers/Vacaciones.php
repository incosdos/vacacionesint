<?php

namespace App\Controllers;

use App\Libraries\CalculoVacaciones;
use App\Libraries\Correo;
use App\Models\UsuarioModel;
use App\Models\VacacionModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * Vacaciones del propio usuario: consultar, solicitar y cancelar.
 */
class Vacaciones extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'vacaciones'];

    private function datosBase(string $titulo): array
    {
        return [
            'titulo'  => $titulo . ' | INTERGRAPHICS',
            'empresa' => config(Intergraphics::class),
            'seccion' => 'vacaciones',
        ];
    }

    private function usuario(): array
    {
        return model(UsuarioModel::class)->find((int) session('idusuario'));
    }

    /** Mis vacaciones */
    public function index(): string
    {
        $calc    = new CalculoVacaciones();
        $usuario = $this->usuario();
        $anio    = (int) date('Y');

        return view('panel/vacaciones/index', $this->datosBase('Mis vacaciones') + [
            'solicitudes' => model(VacacionModel::class)->deUsuario((int) $usuario['idusuario']),
            'saldo'       => $calc->saldo($usuario, $anio),
            'antiguedad'  => $calc->antiguedad($usuario['fecha_ingreso'] ?? null),
            'usuario'     => $usuario,
            'proximos'    => array_slice(array_filter($calc->feriados($anio) + $calc->feriados($anio + 1),
                static fn ($f) => $f >= date('Y-m-d'), ARRAY_FILTER_USE_KEY), 0, 4, true),
        ]);
    }

    /** Formulario de solicitud */
    public function solicitar(): string
    {
        $calc    = new CalculoVacaciones();
        $usuario = $this->usuario();
        $anio    = (int) date('Y');
        $cfg     = $calc->config();

        return view('panel/vacaciones/solicitar', $this->datosBase('Solicitar vacaciones') + [
            'saldos'        => [$anio => $calc->saldo($usuario, $anio), $anio + 1 => $calc->saldo($usuario, $anio + 1)],
            'feriadosJson'  => $calc->feriadosJson($anio, $anio + 1),
            'minimo'        => date('Y-m-d', strtotime("+{$cfg->diasAnticipacion} days")),
            'maximo'        => ($anio + 1) . '-12-31',
            'cfg'           => $cfg,
        ]);
    }

    public function guardar(): RedirectResponse
    {
        $calc    = new CalculoVacaciones();
        $cfg     = $calc->config();
        $usuario = $this->usuario();
        $minimo  = date('Y-m-d', strtotime("+{$cfg->diasAnticipacion} days"));
        $maximo  = ((int) date('Y') + 1) . '-12-31';

        $reglas = [
            'fecha_inicio' => ['rules' => 'required|valid_date[Y-m-d]', 'errors' => [
                'required' => 'Indique la fecha de inicio.', 'valid_date' => 'La fecha de inicio no es válida.']],
            'fecha_fin' => ['rules' => 'required|valid_date[Y-m-d]', 'errors' => [
                'required' => 'Indique la fecha final.', 'valid_date' => 'La fecha final no es válida.']],
            'motivo' => ['rules' => 'permit_empty|max_length[255]', 'errors' => [
                'max_length' => 'El motivo no puede superar 255 caracteres.']],
        ];
        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $inicio = (string) $this->request->getPost('fecha_inicio');
        $fin    = (string) $this->request->getPost('fecha_fin');
        $medio  = $this->request->getPost('medio_dia') === '1' && $inicio === $fin;
        $error  = null;

        if ($inicio < $minimo) {
            $error = $cfg->diasAnticipacion > 0
                ? "Las vacaciones deben solicitarse con al menos {$cfg->diasAnticipacion} día(s) de anticipación (desde el " . vac_fecha($minimo) . ').'
                : 'La fecha de inicio no puede ser anterior a hoy.';
        } elseif ($fin < $inicio) {
            $error = 'La fecha final no puede ser anterior a la fecha de inicio.';
        } elseif ($fin > $maximo) {
            $error = 'Solo puede solicitar vacaciones hasta el ' . vac_fecha($maximo) . '.';
        }

        $dias = $error === null ? $calc->diasHabiles($inicio, $fin, $medio) : 0;

        if ($error === null && $dias <= 0) {
            $error = 'El periodo elegido no tiene días hábiles (solo fines de semana o feriados).';
        }
        if ($error === null && $dias > $cfg->maxDiasPorSolicitud) {
            $error = "Una solicitud no puede superar {$cfg->maxDiasPorSolicitud} días hábiles.";
        }
        if ($error === null && substr($inicio, 0, 4) !== substr($fin, 0, 4)) {
            $error = 'La solicitud no puede abarcar dos gestiones. Divídala en dos solicitudes (una por año).';
        }
        if ($error === null && ($cruce = model(VacacionModel::class)->seSolapa((int) $usuario['idusuario'], $inicio, $fin))) {
            $error = 'Ya tiene una solicitud ' . ($cruce['estado'] === 'pendiente' ? 'pendiente' : 'autorizada')
                . ' que se cruza con esas fechas (' . vac_periodo($cruce['fecha_inicio'], $cruce['fecha_fin']) . ').';
        }
        if ($error === null) {
            $saldo = $calc->saldo($usuario, (int) substr($inicio, 0, 4));
            if ($cfg->bloquearSinSaldo && $saldo['disponibles'] !== null && $dias > $saldo['disponibles']) {
                $error = $saldo['asignados'] === 0
                    ? 'Aún no le corresponden vacaciones: se adquieren al cumplir un año de antigüedad.'
                    : 'La solicitud (' . vac_dias($dias) . ') supera su saldo disponible para la gestión '
                      . $saldo['gestion'] . ' (' . vac_dias($saldo['disponibles']) . ').';
            }
        }

        if ($error !== null) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        $id = model(VacacionModel::class)->insert([
            'idusuario'       => (int) $usuario['idusuario'],
            'fecha_solicitud' => date('Y-m-d'),
            'fecha_inicio'    => $inicio,
            'fecha_fin'       => $fin,
            'dias_habiles'    => $dias,
            'motivo'          => trim((string) $this->request->getPost('motivo')) ?: null,
            'estado'          => 'pendiente',
        ], true);

        $this->notificarAutorizadores($usuario, $inicio, $fin, $dias);

        return redirect()->to(site_url('panel/vacaciones'))
            ->with('exito', 'Solicitud enviada (' . vac_dias($dias) . ' hábiles). Su coordinador recibirá un aviso para revisarla.');
    }

    public function cancelar(int $id): RedirectResponse
    {
        $modelo = model(VacacionModel::class);
        $sol    = $modelo->find($id);

        if ($sol === null || (int) $sol['idusuario'] !== (int) session('idusuario')) {
            return redirect()->to(site_url('panel/vacaciones'))->with('error', 'Solicitud no encontrada.');
        }

        $cancelable = $sol['estado'] === 'pendiente'
            || ($sol['estado'] === 'autorizado' && $sol['fecha_inicio'] > date('Y-m-d'));
        if (! $cancelable) {
            return redirect()->to(site_url('panel/vacaciones'))
                ->with('error', 'Solo puede cancelar solicitudes pendientes o vacaciones autorizadas que aún no comenzaron.');
        }

        $modelo->update($id, ['estado' => 'cancelado']);

        if ($sol['estado'] === 'autorizado') {
            $this->notificarAutorizadores($this->usuario(), $sol['fecha_inicio'], $sol['fecha_fin'], (float) $sol['dias_habiles'], true);
        }

        return redirect()->to(site_url('panel/vacaciones'))->with('exito', 'Solicitud cancelada.');
    }

    private function notificarAutorizadores(array $usuario, string $inicio, string $fin, float $dias, bool $cancelacion = false): void
    {
        $nombre = $usuario['nombres'] . ' ' . $usuario['apellidos'];
        foreach (model(UsuarioModel::class)->autorizadores((int) $usuario['idusuario']) as $a) {
            Correo::notificar(
                $a['email'],
                $a['nombres'],
                $cancelacion ? "Vacaciones canceladas: {$nombre}" : "Nueva solicitud de vacaciones: {$nombre}",
                $cancelacion ? 'Se cancelaron unas vacaciones autorizadas' : 'Tiene una solicitud de vacaciones por revisar',
                [
                    "Colaborador: {$nombre}",
                    'Periodo: ' . vac_periodo($inicio, $fin),
                    'Días hábiles: ' . vac_dias($dias),
                ],
                $cancelacion ? null : 'Revisar solicitud',
                $cancelacion ? null : site_url('panel/solicitudes'),
            );
        }
    }
}
