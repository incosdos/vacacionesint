<?php

namespace App\Controllers;

use App\Libraries\Correo;
use App\Models\RolModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

class Registro extends BaseController
{
    protected $helpers = ['url', 'form', 'ig'];

    /** Formulario de registro */
    public function index(): string
    {
        return view('auth/registro', [
            'titulo'    => 'Registrarse | INTERGRAPHICS',
            'anchoCaja' => 'ig-caja-ancha',
            'empresa' => config(Intergraphics::class),
        ]);
    }

    /** Guarda el usuario (inactivo) y envía el correo de confirmación */
    public function guardar(): RedirectResponse
    {
        $soloLetras = '/^[\p{L}][\p{L} .\'-]*$/u';

        $reglas = [
            'nombres' => [
                'label'  => 'nombres',
                'rules'  => ['required', 'min_length[2]', 'max_length[100]', "regex_match[{$soloLetras}]"],
                'errors' => ['regex_match' => 'Los nombres solo pueden contener letras.'],
            ],
            'apellidos' => [
                'label'  => 'apellidos',
                'rules'  => ['required', 'min_length[2]', 'max_length[100]', "regex_match[{$soloLetras}]"],
                'errors' => ['regex_match' => 'Los apellidos solo pueden contener letras.'],
            ],
            'ci' => [
                'label'  => 'CI',
                'rules'  => ['required', 'regex_match[/^[0-9]{5,12}(-?[0-9A-Za-z]{1,3})?$/]', 'is_unique[usuarios.ci]'],
                'errors' => [
                    'regex_match' => 'El CI debe tener entre 5 y 12 dígitos (puede incluir complemento, p. ej. 1234567-1A).',
                    'is_unique'   => 'Ya existe un usuario registrado con este CI.',
                ],
            ],
            'email' => [
                'label'  => 'correo electrónico',
                'rules'  => 'required|valid_email|max_length[150]|is_unique[usuarios.email]',
                'errors' => ['is_unique' => 'Este correo ya está registrado.'],
            ],
            'celular' => [
                'label'  => 'celular',
                'rules'  => 'required|is_natural_no_zero|min_length[7]|max_length[15]',
                'errors' => ['is_natural_no_zero' => 'El celular solo debe contener números.'],
            ],
            'password' => [
                'label'  => 'contraseña',
                'rules'  => ['required', 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]'],
                'errors' => ['regex_match' => 'La contraseña debe incluir letras y números.'],
            ],
            'password_confirm' => [
                'label'  => 'confirmación de contraseña',
                'rules'  => 'required|matches[password]',
                'errors' => ['matches' => 'Las contraseñas no coinciden.'],
            ],
            'acepto' => [
                'label'  => 'términos',
                'rules'  => 'required',
                'errors' => ['required' => 'Debe aceptar el uso de sus datos para el sistema.'],
            ],
        ];

        $mensajes = [];
        foreach ($reglas as $campo => $r) {
            $mensajes[$campo] = ($r['errors'] ?? []) + [
                'required'   => 'El campo {field} es obligatorio.',
                'min_length' => 'El campo {field} debe tener al menos {param} caracteres.',
                'max_length' => 'El campo {field} no puede superar {param} caracteres.',
                'valid_email'=> 'Ingrese un correo electrónico válido.',
            ];
            unset($reglas[$campo]['errors']);
        }

        if (! $this->validate($reglas, $mensajes)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $usuarios = model(UsuarioModel::class);
        $datos    = [
            'nombres'   => trim((string) $this->request->getPost('nombres')),
            'apellidos' => trim((string) $this->request->getPost('apellidos')),
            'ci'        => strtoupper(trim((string) $this->request->getPost('ci'))),
            'email'     => strtolower(trim((string) $this->request->getPost('email'))),
            'celular'   => (int) $this->request->getPost('celular'),
            'idrol'     => RolModel::INVITADO, // El administrador asignará el rol definitivo
            'password'  => (string) $this->request->getPost('password'),
            'estado'    => 0,                  // Inactivo hasta confirmar el correo
        ];

        $id    = $usuarios->insert($datos, true);
        $token = $usuarios->nuevoTokenVerificacion((int) $id);

        return $this->respuestaEnvio($datos, $token, 'Su cuenta fue creada.');
    }

    /** Enlace que llega por correo */
    public function verificar(string $token = ''): RedirectResponse
    {
        $usuarios = model(UsuarioModel::class);
        $usuario  = preg_match('/^[a-f0-9]{64}$/', $token) ? $usuarios->buscarPorToken($token) : null;

        if ($usuario === null) {
            return redirect()->to(site_url('login'))
                ->with('error', 'El enlace de confirmación no es válido o ya fue utilizado.');
        }

        if (strtotime((string) $usuario['token_expira']) < time()) {
            return redirect()->to(site_url('registro/reenviar'))
                ->with('error', 'El enlace de confirmación venció. Solicite uno nuevo.');
        }

        $usuarios->marcarVerificado((int) $usuario['idusuario']);

        return redirect()->to(site_url('login'))
            ->with('exito', '¡Correo confirmado! Ya puede iniciar sesión.');
    }

    /** Formulario para reenviar el correo de confirmación */
    public function reenviarForm(): string
    {
        return view('auth/reenviar', [
            'titulo'  => 'Reenviar confirmación | INTERGRAPHICS',
            'empresa' => config(Intergraphics::class),
        ]);
    }

    public function reenviar(): RedirectResponse
    {
        if (! $this->validate(['email' => 'required|valid_email'], ['email' => [
            'required'    => 'Ingrese su correo electrónico.',
            'valid_email' => 'Ingrese un correo electrónico válido.',
        ]])) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $usuarios = model(UsuarioModel::class);
        $usuario  = $usuarios->where('email', strtolower(trim((string) $this->request->getPost('email'))))->first();

        // Mensaje genérico: no revela si el correo existe
        $generico = 'Si el correo está registrado y pendiente de confirmación, recibirá un nuevo enlace en unos minutos.';

        if ($usuario === null || $usuario['email_verificado_en'] !== null) {
            return redirect()->to(site_url('login'))->with('info', $generico);
        }

        $token = $usuarios->nuevoTokenVerificacion((int) $usuario['idusuario']);

        return $this->respuestaEnvio($usuario, $token, $generico);
    }

    /**
     * Envía el correo y arma el mensaje para el usuario.
     * En desarrollo, si no hay SMTP configurado, muestra el enlace en pantalla.
     */
    private function respuestaEnvio(array $usuario, string $token, string $mensaje): RedirectResponse
    {
        $redir = redirect()->to(site_url('login'));

        if (Correo::enviarVerificacion($usuario, $token)) {
            return $redir->with('exito', $mensaje . ' Le enviamos un correo a ' . $usuario['email']
                . ' con el enlace para confirmar su registro (válido por 24 horas).');
        }

        if (ENVIRONMENT === 'development') {
            return $redir->with('info', $mensaje . ' [Modo desarrollo] El correo no se envió porque falta configurar SMTP. Use este enlace para confirmar:')
                ->with('enlace_dev', site_url('registro/verificar/' . $token));
        }

        return $redir->with('error', $mensaje . ' Sin embargo, no pudimos enviar el correo de confirmación. '
            . 'Intente "Reenviar correo de confirmación" más tarde o contacte al administrador.');
    }
}
