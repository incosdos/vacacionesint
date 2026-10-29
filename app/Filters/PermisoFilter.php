<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Exige uno de los permisos indicados.
 * Uso en rutas: ['filter' => 'permiso:vacaciones.autorizar']
 */
class PermisoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('idusuario')) {
            return redirect()->to(site_url('login'))->with('error', 'Inicie sesión para continuar.');
        }

        $permisos = session('permisos') ?? [];

        foreach ((array) $arguments as $permiso) {
            if (in_array($permiso, $permisos, true)) {
                return;
            }
        }

        return redirect()->to(site_url('panel'))->with('error', 'No tiene permiso para acceder a esa sección.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
