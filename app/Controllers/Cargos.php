<?php

namespace App\Controllers;

use App\Models\CargoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * CRUD de cargos (permiso cargos.gestionar, solo Administrador).
 */
class Cargos extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'usuarios'];

    private function datosBase(string $titulo): array
    {
        return [
            'titulo'  => $titulo,
            'empresa' => config(Intergraphics::class),
            'seccion' => 'cargos',
        ];
    }

    private function buscarOFallar(int $id): array
    {
        return model(CargoModel::class)->find($id)
            ?? throw PageNotFoundException::forPageNotFound('El cargo no existe.');
    }

    public function index(): string
    {
        $q      = trim((string) $this->request->getGet('q'));
        $cargos = model(CargoModel::class)->listarConConteos($q);

        return view('panel/cargos/index', $this->datosBase('Cargos') + [
            'cargos'  => $cargos,
            'q'       => $q,
            'totales' => [
                'cargos'   => count($cargos),
                'activos'  => count(array_filter($cargos, static fn ($c) => (int) $c['estado'] === 1)),
                'usuarios' => array_sum(array_column($cargos, 'total_usuarios')),
                'sinCargo' => db_connect()->table('usuarios')->where('idcargo', null)->countAllResults(),
            ],
        ]);
    }

    public function nuevo(): string
    {
        return view('panel/cargos/form', $this->datosBase('Nuevo cargo') + [
            'c'        => ['idcargo' => null, 'nombre' => '', 'descripcion' => '', 'estado' => 1],
            'usuarios' => [],
        ]);
    }

    public function crear(): RedirectResponse
    {
        if (! $this->validate($this->reglas())) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        model(CargoModel::class)->insert($this->datosFormulario());

        return redirect()->to(site_url('panel/cargos'))->with('exito', 'Cargo «' . $this->datosFormulario()['nombre'] . '» creado.');
    }

    public function editar(int $id): string
    {
        $c = $this->buscarOFallar($id);

        return view('panel/cargos/form', $this->datosBase('Editar cargo') + [
            'c'        => $c,
            'usuarios' => model(CargoModel::class)->usuariosDe($id),
        ]);
    }

    public function actualizar(int $id): RedirectResponse
    {
        $this->buscarOFallar($id);

        if (! $this->validate($this->reglas($id))) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        model(CargoModel::class)->update($id, $this->datosFormulario());

        return redirect()->to(site_url('panel/cargos'))->with('exito', 'Cargo actualizado.');
    }

    public function cambiarEstado(int $id): RedirectResponse
    {
        $c     = $this->buscarOFallar($id);
        $nuevo = (int) $c['estado'] === 1 ? 0 : 1;
        model(CargoModel::class)->update($id, ['estado' => $nuevo]);

        return redirect()->to(site_url('panel/cargos'))->with('exito', $nuevo
            ? "Cargo «{$c['nombre']}» activado."
            : "Cargo «{$c['nombre']}» desactivado. Ya no aparecerá al asignar cargos; quienes lo tienen lo conservan.");
    }

    public function eliminar(int $id): RedirectResponse
    {
        $c = $this->buscarOFallar($id);
        $n = model(CargoModel::class)->totalUsuarios($id);

        if ($n > 0) {
            return redirect()->to(site_url('panel/cargos'))
                ->with('error', "No se puede eliminar «{$c['nombre']}»: tiene {$n} usuario(s) asignado(s). Cámbielos de cargo o desactive el cargo.");
        }

        model(CargoModel::class)->delete($id);

        return redirect()->to(site_url('panel/cargos'))->with('exito', "Cargo «{$c['nombre']}» eliminado.");
    }

    private function reglas(?int $id = null): array
    {
        $unico = $id === null ? 'is_unique[cargos.nombre]' : "is_unique[cargos.nombre,idcargo,{$id}]";

        return [
            'nombre' => [
                'rules'  => ['required', 'min_length[3]', 'max_length[80]', 'regex_match[/^[\p{L}0-9][\p{L}0-9 .\-\/()]*$/u]', $unico],
                'errors' => [
                    'required'    => 'El nombre del cargo es obligatorio.',
                    'min_length'  => 'El nombre debe tener al menos 3 caracteres.',
                    'max_length'  => 'El nombre no puede superar 80 caracteres.',
                    'regex_match' => 'El nombre solo puede contener letras, números, espacios y . - / ( ).',
                    'is_unique'   => 'Ya existe un cargo con ese nombre.',
                ],
            ],
            'descripcion' => [
                'rules'  => 'permit_empty|max_length[255]',
                'errors' => ['max_length' => 'La descripción no puede superar 255 caracteres.'],
            ],
            'estado' => ['rules' => 'required|in_list[0,1]', 'errors' => ['in_list' => 'Estado no válido.']],
        ];
    }

    private function datosFormulario(): array
    {
        $desc = trim((string) $this->request->getPost('descripcion'));

        return [
            'nombre'      => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('nombre'))),
            'descripcion' => $desc === '' ? null : $desc,
            'estado'      => (int) $this->request->getPost('estado') === 1 ? 1 : 0,
        ];
    }
}
