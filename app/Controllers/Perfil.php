<?php

namespace App\Controllers;

use App\Libraries\CalculoVacaciones;
use App\Libraries\Correo;
use App\Models\PermisoModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * Mi perfil (permiso perfil.editar): datos propios y cambio de contraseña.
 * El correo y el CI solo los cambia el administrador.
 */
class Perfil extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'usuarios', 'vacaciones'];

    private function yo(): array
    {
        return model(UsuarioModel::class)->conRol((int) session('idusuario'));
    }

    public function index(): string
    {
        $u    = $this->yo();
        $calc = new CalculoVacaciones();

        $claves   = session('permisos') ?? [];
        $permisos = $claves === [] ? [] : model(PermisoModel::class)->whereIn('clave', $claves)->orderBy('idpermiso')->findAll();

        return view('panel/perfil/index', [
            'titulo'   => 'Mi perfil | INTERGRAPHICS',
            'empresa'  => config(Intergraphics::class),
            'seccion'  => 'perfil',
            'u'        => $u,
            'antig'    => $calc->antiguedad($u['fecha_ingreso'] ?? null),
            'saldo'    => tiene_permiso('vacaciones.ver_propias') ? $calc->saldo($u, (int) date('Y')) : null,
            'permisos' => $permisos,
            'pestana'  => session('pestana') ?? 'datos',
        ]);
    }

    /** Actualiza nombres, apellidos y celular. */
    public function actualizar(): RedirectResponse
    {
        $letras = '/^[\p{L}][\p{L} .\'-]*$/u';
        $reglas = [
            'nombres' => ['rules' => ['required', 'min_length[2]', 'max_length[100]', "regex_match[{$letras}]"], 'errors' => [
                'required' => 'Ingrese sus nombres.', 'min_length' => 'Los nombres deben tener al menos 2 letras.',
                'max_length' => 'Los nombres no pueden superar 100 caracteres.', 'regex_match' => 'Los nombres solo pueden contener letras.']],
            'apellidos' => ['rules' => ['required', 'min_length[2]', 'max_length[100]', "regex_match[{$letras}]"], 'errors' => [
                'required' => 'Ingrese sus apellidos.', 'min_length' => 'Los apellidos deben tener al menos 2 letras.',
                'max_length' => 'Los apellidos no pueden superar 100 caracteres.', 'regex_match' => 'Los apellidos solo pueden contener letras.']],
            'celular' => ['rules' => 'required|is_natural_no_zero|min_length[7]|max_length[15]', 'errors' => [
                'required' => 'Ingrese su celular.', 'is_natural_no_zero' => 'El celular solo debe contener números.',
                'min_length' => 'El celular debe tener al menos 7 dígitos.', 'max_length' => 'El celular no puede tener más de 15 dígitos.']],
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors())->with('pestana', 'datos');
        }

        $datos = [
            'nombres'   => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('nombres'))),
            'apellidos' => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('apellidos'))),
            'celular'   => (int) $this->request->getPost('celular'),
        ];
        model(UsuarioModel::class)->update((int) session('idusuario'), $datos);
        session()->set('nombre', $datos['nombres'] . ' ' . $datos['apellidos']);

        return redirect()->to(site_url('panel/perfil'))->with('exito', 'Sus datos fueron actualizados.');
    }

    /** Cambio de contraseña: exige la actual. */
    public function cambiarPassword(): RedirectResponse
    {
        $reglas = [
            'password_actual' => ['rules' => 'required', 'errors' => ['required' => 'Ingrese su contraseña actual.']],
            'password' => ['rules' => ['required', 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]'], 'errors' => [
                'required' => 'Ingrese la nueva contraseña.', 'min_length' => 'La nueva contraseña debe tener al menos 8 caracteres.',
                'max_length' => 'La nueva contraseña no puede superar 72 caracteres.', 'regex_match' => 'La nueva contraseña debe incluir letras y números.']],
            'password_confirm' => ['rules' => 'required|matches[password]', 'errors' => [
                'required' => 'Confirme la nueva contraseña.', 'matches' => 'Las contraseñas nuevas no coinciden.']],
        ];

        $volver = redirect()->to(site_url('panel/perfil'))->with('pestana', 'seguridad');

        if (! $this->validate($reglas)) {
            return $volver->with('errores', $this->validator->getErrors());
        }

        $u      = model(UsuarioModel::class)->find((int) session('idusuario'));
        $actual = (string) $this->request->getPost('password_actual');
        $nueva  = (string) $this->request->getPost('password');

        if (! password_verify($actual, $u['password'])) {
            return $volver->with('errores', ['password_actual' => 'La contraseña actual no es correcta.']);
        }
        if (password_verify($nueva, $u['password'])) {
            return $volver->with('errores', ['password' => 'La nueva contraseña debe ser distinta a la actual.']);
        }

        model(UsuarioModel::class)->update((int) $u['idusuario'], ['password' => $nueva]);
        session()->regenerate(true);

        Correo::notificar(
            $u['email'],
            $u['nombres'],
            'Su contraseña fue cambiada',
            'Su contraseña fue cambiada',
            [
                'La contraseña de su cuenta en el Sistema de Vacaciones se cambió el ' . date('d/m/Y \a \l\a\s H:i') . '.',
                'Si no fue usted, comuníquese de inmediato con el administrador.',
            ],
        );

        return $volver->with('exito', 'Su contraseña fue cambiada correctamente.');
    }
}
