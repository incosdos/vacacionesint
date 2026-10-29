<?php

namespace App\Controllers;

use App\Models\PermisoModel;
use App\Models\RolModel;
use App\Models\UsuarioModel;
use Config\Intergraphics;

class Panel extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth'];

    public function index(): string
    {
        $claves   = session('permisos') ?? [];
        $permisos = $claves === [] ? [] : model(PermisoModel::class)->whereIn('clave', $claves)->orderBy('clave')->findAll();

        $invitados = 0;
        if (tiene_permiso('usuarios.gestionar')) {
            $invitados = model(UsuarioModel::class)
                ->where('idrol', RolModel::INVITADO)->where('estado', 1)->countAllResults();
        }

        return view('panel/index', [
            'invitadosPendientes' => $invitados,
            'titulo'   => 'Panel | INTERGRAPHICS',
            'empresa'  => config(Intergraphics::class),
            'usuario'  => usuario_actual(),
            'permisos' => $permisos,
        ]);
    }
}
