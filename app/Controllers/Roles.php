<?php

namespace App\Controllers;

use App\Models\PermisoModel;
use App\Models\RolModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * Módulo de roles y permisos (requiere el permiso roles.gestionar).
 */
class Roles extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth'];

    private function datosBase(string $titulo): array
    {
        return [
            'titulo'  => $titulo . ' | INTERGRAPHICS',
            'empresa' => config(Intergraphics::class),
            'seccion' => 'roles',
        ];
    }

    /** Listado de roles */
    public function index(): string
    {
        return view('panel/roles/index', $this->datosBase('Roles y permisos') + [
            'roles'          => model(RolModel::class)->listarConConteos(),
            'totalPermisos'  => model(PermisoModel::class)->countAllResults(),
        ]);
    }

    /** Formulario de nuevo rol */
    public function nuevo(): string
    {
        return view('panel/roles/form', $this->datosBase('Nuevo rol') + [
            'rol'          => ['idrol' => null, 'nombre' => '', 'descripcion' => ''],
            'grupos'       => model(PermisoModel::class)->agrupados(),
            // Por defecto todo rol nuevo puede entrar al panel y ver su perfil
            'seleccionados'=> array_map('intval', model(PermisoModel::class)
                ->whereIn('clave', ['panel.ver', 'perfil.editar'])->findColumn('idpermiso') ?? []),
            'totalUsuarios'=> 0,
        ]);
    }

    public function crear(): RedirectResponse
    {
        if (! $this->validate($this->reglas())) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $roles = model(RolModel::class);
        $id    = (int) $roles->insert($this->datosFormulario(), true);
        $roles->asignarPermisos($id, (array) $this->request->getPost('permisos'));

        return redirect()->to(site_url('panel/roles'))->with('exito', 'Rol creado correctamente.');
    }

    /** Formulario de edición */
    public function editar(int $id): string
    {
        $roles = model(RolModel::class);
        $rol   = $roles->find($id) ?? throw PageNotFoundException::forPageNotFound('El rol no existe.');

        return view('panel/roles/form', $this->datosBase('Editar rol') + [
            'rol'           => $rol,
            'grupos'        => model(PermisoModel::class)->agrupados(),
            'seleccionados' => $roles->idsPermisos($id),
            'totalUsuarios' => $roles->totalUsuarios($id),
        ]);
    }

    public function actualizar(int $id): RedirectResponse
    {
        $roles = model(RolModel::class);
        if ($roles->find($id) === null) {
            throw PageNotFoundException::forPageNotFound('El rol no existe.');
        }

        if (! $this->validate($this->reglas($id))) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = $this->datosFormulario();
        if ($id === RolModel::ADMINISTRADOR) {
            unset($datos['nombre']); // El nombre del rol Administrador no se cambia
        }

        $roles->update($id, $datos);
        $roles->asignarPermisos($id, (array) $this->request->getPost('permisos'));

        return redirect()->to(site_url('panel/roles'))->with('exito', 'Rol actualizado correctamente.');
    }

    public function eliminar(int $id): RedirectResponse
    {
        $roles = model(RolModel::class);
        $rol   = $roles->find($id);

        if ($rol === null) {
            return redirect()->to(site_url('panel/roles'))->with('error', 'El rol no existe.');
        }
        if (RolModel::esProtegido($id)) {
            return redirect()->to(site_url('panel/roles'))->with('error', 'Los roles base del sistema no se pueden eliminar.');
        }
        if (($n = $roles->totalUsuarios($id)) > 0) {
            return redirect()->to(site_url('panel/roles'))
                ->with('error', "No se puede eliminar «{$rol['nombre']}»: tiene {$n} usuario(s) asignado(s). Cámbielos de rol primero.");
        }

        $roles->delete($id); // rol_permiso se borra en cascada

        return redirect()->to(site_url('panel/roles'))->with('exito', "Rol «{$rol['nombre']}» eliminado.");
    }

    /** Matriz roles × permisos */
    public function matriz(): string
    {
        return view('panel/roles/matriz', $this->datosBase('Matriz de permisos') + [
            'roles'  => model(RolModel::class)->orderBy('idrol')->findAll(),
            'grupos' => model(PermisoModel::class)->agrupados(),
            'mapa'   => model(RolModel::class)->mapaPermisos(),
        ]);
    }

    public function guardarMatriz(): RedirectResponse
    {
        $roles   = model(RolModel::class);
        $enviado = (array) $this->request->getPost('permisos');

        foreach ($roles->findColumn('idrol') ?? [] as $idrol) {
            $roles->asignarPermisos((int) $idrol, (array) ($enviado[$idrol] ?? []));
        }

        return redirect()->to(site_url('panel/roles/matriz'))->with('exito', 'Permisos actualizados para todos los roles.');
    }

    private function reglas(?int $id = null): array
    {
        $unico = $id === null ? 'is_unique[roles.nombre]' : "is_unique[roles.nombre,idrol,{$id}]";
        $reglas = [
            'descripcion' => [
                'rules'  => 'permit_empty|max_length[255]',
                'errors' => ['max_length' => 'La descripción no puede superar 255 caracteres.'],
            ],
            'permisos.*' => [
                'rules'  => 'permit_empty|is_natural_no_zero',
                'errors' => ['is_natural_no_zero' => 'Permiso no válido.'],
            ],
        ];

        if ($id !== RolModel::ADMINISTRADOR) {
            $reglas['nombre'] = [
                'rules'  => ['required', 'min_length[3]', 'max_length[50]', 'regex_match[/^[\p{L}0-9 ._-]+$/u]', $unico],
                'errors' => [
                    'required'    => 'El nombre del rol es obligatorio.',
                    'min_length'  => 'El nombre debe tener al menos 3 caracteres.',
                    'max_length'  => 'El nombre no puede superar 50 caracteres.',
                    'regex_match' => 'El nombre solo puede contener letras, números, espacios, puntos y guiones.',
                    'is_unique'   => 'Ya existe un rol con ese nombre.',
                ],
            ];
        }

        return $reglas;
    }

    private function datosFormulario(): array
    {
        $desc = trim((string) $this->request->getPost('descripcion'));

        return [
            'nombre'      => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('nombre'))),
            'descripcion' => $desc === '' ? null : $desc,
        ];
    }
}
