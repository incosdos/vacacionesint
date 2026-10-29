<?php

namespace App\Controllers;

use App\Models\PermisoModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

class Auth extends BaseController
{
    protected $helpers = ['url', 'form', 'ig'];

    public function login(): string
    {
        return view('auth/login', [
            'titulo'  => 'Iniciar sesión | INTERGRAPHICS',
            'empresa' => config(Intergraphics::class),
        ]);
    }

    public function autenticar(): RedirectResponse
    {
        $reglas = [
            'usuario'  => ['label' => 'usuario', 'rules' => 'required|max_length[150]'],
            'password' => ['label' => 'contraseña', 'rules' => 'required|max_length[255]'],
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', 'Ingrese su usuario y contraseña.');
        }

        $usuarios = model(UsuarioModel::class);
        $usuario  = $usuarios->buscarParaLogin(trim((string) $this->request->getPost('usuario')));

        if ($usuario === null || ! password_verify((string) $this->request->getPost('password'), $usuario['password'])) {
            return redirect()->back()->withInput()->with('error', 'Usuario o contraseña incorrectos.');
        }

        if ($usuario['email_verificado_en'] === null) {
            return redirect()->back()->withInput()
                ->with('error', 'Debe confirmar su correo electrónico antes de ingresar.')
                ->with('mostrar_reenviar', true);
        }

        if ((int) $usuario['estado'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Su cuenta está inactiva. Contacte al administrador.');
        }

        // Actualiza el hash si PHP recomienda uno más fuerte
        if (password_needs_rehash($usuario['password'], PASSWORD_DEFAULT)) {
            $usuarios->update($usuario['idusuario'], ['password' => (string) $this->request->getPost('password')]);
        }
        $usuarios->update($usuario['idusuario'], ['ultimo_acceso' => date('Y-m-d H:i:s')]);

        $session = session();
        $session->regenerate(true);
        $session->set([
            'idusuario' => (int) $usuario['idusuario'],
            'nombre'    => $usuario['nombres'] . ' ' . $usuario['apellidos'],
            'email'     => $usuario['email'],
            'idrol'     => (int) $usuario['idrol'],
            'rol'       => $usuario['rol'],
            'permisos'  => model(PermisoModel::class)->clavesPorRol((int) $usuario['idrol']),
        ]);

        return redirect()->to(site_url('panel'));
    }

    public function salir(): RedirectResponse
    {
        $session = session();
        $session->remove(['idusuario', 'nombre', 'email', 'idrol', 'rol', 'permisos']);
        $session->regenerate(true); // nuevo ID de sesión, se descarta el anterior

        return redirect()->to(site_url('login'))->with('exito', 'Sesión cerrada correctamente.');
    }
}
