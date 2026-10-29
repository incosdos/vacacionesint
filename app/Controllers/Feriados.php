<?php

namespace App\Controllers;

use App\Libraries\CalculoVacaciones;
use App\Models\FeriadoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * Feriados y días no laborables (permiso feriados.gestionar, solo Administrador).
 */
class Feriados extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'vacaciones'];

    private function datosBase(string $titulo): array
    {
        return ['titulo' => $titulo, 'empresa' => config(Intergraphics::class), 'seccion' => 'feriados'];
    }

    private function anioSolicitado(): int
    {
        $anio = (int) ($this->request->getGet('anio') ?? date('Y'));

        return $anio >= 2000 && $anio <= 2100 ? $anio : (int) date('Y');
    }

    private function buscarOFallar(int $id): array
    {
        return model(FeriadoModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound('El feriado no existe.');
    }

    public function index(): string
    {
        $anio  = $this->anioSolicitado();
        $tipo  = (string) $this->request->getGet('tipo');
        $model = model(FeriadoModel::class);
        $calc  = new CalculoVacaciones();
        $todos = $model->delAnio($anio);

        $conteo = ['total' => count($todos), 'habiles' => 0] + array_fill_keys(array_keys(CalculoVacaciones::TIPOS), 0);
        foreach ($todos as $f) {
            $conteo[$f['tipo']]++;
            if ((int) date('N', strtotime($f['fecha'])) <= 5) {
                $conteo['habiles']++;
            }
        }

        $existentes = array_column($todos, 'fecha');
        $faltantes  = array_filter($calc->propuestaAnio($anio), static fn ($p) => ! in_array($p['fecha'], $existentes, true));

        return view('panel/feriados/index', $this->datosBase('Feriados') + [
            'anio'      => $anio,
            'anios'     => $model->anios(),
            'tipo'      => $tipo,
            'feriados'  => $tipo === '' ? $todos : $model->delAnio($anio, $tipo),
            'conteo'    => $conteo,
            'faltantes' => array_values($faltantes),
            'tipos'     => CalculoVacaciones::TIPOS,
        ]);
    }

    public function nuevo(): string
    {
        $fecha = (string) $this->request->getGet('fecha');

        return view('panel/feriados/form', $this->datosBase('Nuevo feriado') + [
            'f'     => ['idferiado' => null, 'fecha' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) ? $fecha : '', 'nombre' => '', 'tipo' => 'nacional', 'observacion' => ''],
            'tipos' => CalculoVacaciones::TIPOS,
        ]);
    }

    public function crear(): RedirectResponse
    {
        if (! $this->validate($this->reglas())) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = $this->datosFormulario() + ['registrado_por' => (int) session('idusuario')];
        $model = model(FeriadoModel::class);
        $model->insert($datos);
        $n = $model->recalcularVacaciones([$datos['fecha']]);

        return $this->volverAlAnio($datos['fecha'])->with('exito', 'Se registró «' . $datos['nombre'] . '» el ' . vac_fecha($datos['fecha']) . '.' . $this->avisoRecalculo($n));
    }

    public function editar(int $id): string
    {
        return view('panel/feriados/form', $this->datosBase('Editar feriado') + [
            'f'     => $this->buscarOFallar($id),
            'tipos' => CalculoVacaciones::TIPOS,
        ]);
    }

    public function actualizar(int $id): RedirectResponse
    {
        $antes = $this->buscarOFallar($id);

        if (! $this->validate($this->reglas($id))) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = $this->datosFormulario();
        $model = model(FeriadoModel::class);
        $model->update($id, $datos);
        $n = $model->recalcularVacaciones([$antes['fecha'], $datos['fecha']]);

        return $this->volverAlAnio($datos['fecha'])->with('exito', 'Feriado actualizado.' . $this->avisoRecalculo($n));
    }

    public function eliminar(int $id): RedirectResponse
    {
        $f     = $this->buscarOFallar($id);
        $model = model(FeriadoModel::class);
        $model->delete($id);
        $n = $model->recalcularVacaciones([$f['fecha']]);

        return $this->volverAlAnio($f['fecha'])->with('exito', '«' . $f['nombre'] . '» (' . vac_fecha($f['fecha']) . ') ya no es feriado: ahora es día hábil.' . $this->avisoRecalculo($n));
    }

    /** Agrega los feriados calculados del año que aún no estén registrados. */
    public function generar(): RedirectResponse
    {
        $anio = (int) $this->request->getPost('anio');
        if ($anio < 2000 || $anio > 2100) {
            return redirect()->to(site_url('panel/feriados'))->with('error', 'Año no válido.');
        }

        $model      = model(FeriadoModel::class);
        $existentes = array_column($model->delAnio($anio), 'fecha');
        $nuevos     = [];
        foreach ((new CalculoVacaciones())->propuestaAnio($anio) as $p) {
            if (! in_array($p['fecha'], $existentes, true)) {
                $model->insert($p + ['registrado_por' => (int) session('idusuario')]);
                $nuevos[] = $p['fecha'];
            }
        }
        $n = $model->recalcularVacaciones($nuevos);

        $volver = redirect()->to(site_url('panel/feriados?anio=' . $anio));

        return $nuevos === []
            ? $volver->with('info', "Los feriados calculados de {$anio} ya estaban todos registrados.")
            : $volver->with('exito', 'Se agregaron ' . count($nuevos) . " feriado(s) de {$anio}. Revíselos y ajústelos según los decretos del gobierno." . $this->avisoRecalculo($n));
    }

    private function volverAlAnio(string $fecha): RedirectResponse
    {
        return redirect()->to(site_url('panel/feriados?anio=' . substr($fecha, 0, 4)));
    }

    private function avisoRecalculo(int $n): string
    {
        return $n > 0 ? " Se recalcularon los días hábiles de {$n} solicitud(es) de vacaciones que incluyen esa fecha." : '';
    }

    private function reglas(?int $id = null): array
    {
        $unico = $id === null ? 'is_unique[feriados.fecha]' : "is_unique[feriados.fecha,idferiado,{$id}]";

        return [
            'fecha' => ['rules' => "required|valid_date[Y-m-d]|{$unico}", 'errors' => [
                'required' => 'Indique la fecha.', 'valid_date' => 'La fecha no es válida.',
                'is_unique' => 'Ya hay un feriado registrado en esa fecha. Edítelo en lugar de crear otro.']],
            'nombre' => ['rules' => 'required|min_length[3]|max_length[120]', 'errors' => [
                'required' => 'Indique el nombre o motivo.', 'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                'max_length' => 'El nombre no puede superar 120 caracteres.']],
            'tipo' => ['rules' => 'required|in_list[' . implode(',', array_keys(CalculoVacaciones::TIPOS)) . ']', 'errors' => [
                'required' => 'Elija el tipo.', 'in_list' => 'Tipo no válido.']],
            'observacion' => ['rules' => 'permit_empty|max_length[255]', 'errors' => [
                'max_length' => 'La observación no puede superar 255 caracteres.']],
        ];
    }

    private function datosFormulario(): array
    {
        $obs = trim((string) $this->request->getPost('observacion'));

        return [
            'fecha'       => (string) $this->request->getPost('fecha'),
            'nombre'      => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('nombre'))),
            'tipo'        => (string) $this->request->getPost('tipo'),
            'observacion' => $obs === '' ? null : $obs,
        ];
    }
}
