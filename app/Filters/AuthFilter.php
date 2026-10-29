<?php

namespace App\Filters;

use App\Models\PermisoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Exige sesión iniciada y, en cada petición, recarga el rol y los permisos
 * desde la base de datos (los cambios del administrador se aplican al instante).
 * Si la cuenta fue desactivada, cierra la sesión.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $id      = (int) $session->get('idusuario');

        if ($id === 0) {
            return redirect()->to(site_url('login'))->with('error', 'Inicie sesión para continuar.');
        }

        $usuario = model(UsuarioModel::class)
            ->select('usuarios.idrol, usuarios.estado, roles.nombre AS rol')
            ->join('roles', 'roles.idrol = usuarios.idrol')
            ->find($id);

        if ($usuario === null || (int) $usuario['estado'] !== 1) {
            $session->remove(['idusuario', 'nombre', 'email', 'idrol', 'rol', 'permisos']);
            $session->regenerate(true);

            return redirect()->to(site_url('login'))->with('error', 'Su sesión fue cerrada porque la cuenta está inactiva.');
        }

        $session->set([
            'idrol'    => (int) $usuario['idrol'],
            'rol'      => $usuario['rol'],
            'permisos' => model(PermisoModel::class)->clavesPorRol((int) $usuario['idrol']),
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
