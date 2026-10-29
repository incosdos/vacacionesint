<?php

namespace App\Controllers;

use App\Libraries\Correo;
use App\Models\CargoModel;
use App\Models\RolModel;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Intergraphics;

/**
 * Módulo de usuarios.
 *  - usuarios.ver       : listado y detalle
 *  - usuarios.gestionar : crear, editar, asignar rol, activar/desactivar, eliminar
 */
class Usuarios extends BaseController
{
    protected $helpers = ['url', 'form', 'ig', 'auth', 'usuarios', 'vacaciones'];

    private const POR_PAGINA = 15;

    private function datosBase(string $titulo): array
    {
        return [
            'titulo'  => $titulo . ' | INTERGRAPHICS',
            'empresa' => config(Intergraphics::class),
            'seccion' => 'usuarios',
        ];
    }

    private function buscarOFallar(int $id): array
    {
        return model(UsuarioModel::class)->conRol($id)
            ?? throw PageNotFoundException::forPageNotFound('El usuario no existe.');
    }

    private function volver(): RedirectResponse
    {
        // Regresa al listado conservando filtros y página si venimos de ahí
        $previa = (string) previous_url();

        return redirect()->to(str_contains($previa, '/panel/usuarios') && ! str_contains($previa, '/editar') && ! str_contains($previa, '/nuevo')
            ? $previa : site_url('panel/usuarios'));
    }

    /* =============================== Consulta =============================== */

    public function index(): string
    {
        $filtros = [
            'q'      => trim((string) $this->request->getGet('q')),
            'rol'    => (string) $this->request->getGet('rol'),
            'cargo'  => (string) $this->request->getGet('cargo'),
            'estado' => (string) $this->request->getGet('estado'),
        ];

        $modelo   = model(UsuarioModel::class);
        $usuarios = $modelo->filtrar($filtros)->paginate(self::POR_PAGINA);

        return view('panel/usuarios/index', $this->datosBase('Usuarios') + [
            'usuarios' => $usuarios,
            'pager'    => $modelo->pager,
            'filtros'  => $filtros,
            'roles'    => model(RolModel::class)->orderBy('idrol')->findAll(),
            'cargos'   => model(CargoModel::class)->orderBy('nombre')->findAll(),
            'stats'    => model(UsuarioModel::class)->estadisticas(),
        ]);
    }

    public function ver(int $id): string
    {
        $usuario = $this->buscarOFallar($id);

        $vac = db_connect()->table('vacaciones')
            ->select("COUNT(*) total, SUM(estado = 'pendiente') pendientes, SUM(estado = 'autorizado') autorizadas,
                      COALESCE(SUM(CASE WHEN estado = 'autorizado' THEN dias_habiles END), 0) dias", false)
            ->where('idusuario', $id)->get()->getRowArray();

        return view('panel/usuarios/ver', $this->datosBase($usuario['nombres'] . ' ' . $usuario['apellidos']) + [
            'u'      => $usuario,
            'vac'    => $vac,
            'saldo'  => (new \App\Libraries\CalculoVacaciones())->saldo($usuario, (int) date('Y')),
            'antig'  => (new \App\Libraries\CalculoVacaciones())->antiguedad($usuario['fecha_ingreso'] ?? null),
            'roles'  => model(RolModel::class)->orderBy('idrol')->findAll(),
        ]);
    }

    /* =============================== Alta / edición =============================== */

    public function nuevo(): string
    {
        return view('panel/usuarios/form', $this->datosBase('Nuevo usuario') + [
            'u'     => ['idusuario' => null, 'nombres' => '', 'apellidos' => '', 'ci' => '', 'email' => '', 'celular' => '', 'fecha_ingreso' => '',
                        'idrol' => RolModel::COLABORADOR, 'idcargo' => null, 'estado' => 1],
            'roles' => model(RolModel::class)->orderBy('idrol')->findAll(),
            'cargos'=> model(CargoModel::class)->paraSelect(),
        ]);
    }

    public function crear(): RedirectResponse
    {
        if (($e = $this->fechaIngresoFutura()) !== null) {
            return redirect()->back()->withInput()->with('errores', ['fecha_ingreso' => $e]);
        }
        if (! $this->validate($this->reglas(null))) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = $this->datosFormulario();
        if (! tiene_permiso('cargos.gestionar')) {
            $datos['idcargo'] = null; // Solo el Administrador asigna cargos
        }
        $datos += [
            'password'            => (string) $this->request->getPost('password'),
            'email_verificado_en' => date('Y-m-d H:i:s'), // Creado por el administrador: correo de confianza
        ];

        $id = model(UsuarioModel::class)->insert($datos, true);

        return redirect()->to(site_url("panel/usuarios/{$id}"))
            ->with('exito', 'Usuario creado. Comparta la contraseña inicial con la persona y pídale que la cambie.');
    }

    public function editar(int $id): string
    {
        $u = $this->buscarOFallar($id);

        return view('panel/usuarios/form', $this->datosBase('Editar usuario') + [
            'u'      => $u,
            'roles'  => model(RolModel::class)->orderBy('idrol')->findAll(),
            'cargos' => model(CargoModel::class)->paraSelect($u['idcargo'] ? (int) $u['idcargo'] : null),
        ]);
    }

    public function actualizar(int $id): RedirectResponse
    {
        $actual = $this->buscarOFallar($id);

        if (($e = $this->fechaIngresoFutura()) !== null) {
            return redirect()->back()->withInput()->with('errores', ['fecha_ingreso' => $e]);
        }
        if (! $this->validate($this->reglas($id))) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $datos = $this->datosFormulario();

        // Solo el Administrador (permiso cargos.gestionar) asigna cargos
        if (! tiene_permiso('cargos.gestionar')) {
            unset($datos['idcargo']);
        }

        // Uno mismo no puede cambiarse el rol ni desactivarse
        if ($id === (int) session('idusuario')) {
            $datos['idrol']  = (int) $actual['idrol'];
            $datos['estado'] = 1;
        }

        if ($error = $this->violaUltimoAdmin($actual, (int) $datos['idrol'], (int) $datos['estado'])) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        // Activar a alguien que no confirmó su correo: el administrador lo da por verificado
        if ((int) $datos['estado'] === 1 && $actual['email_verificado_en'] === null) {
            $datos['email_verificado_en'] = date('Y-m-d H:i:s');
            $datos['token_verificacion']  = null;
            $datos['token_expira']        = null;
        }

        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $datos['password'] = $password;
        }

        model(UsuarioModel::class)->update($id, $datos);

        return redirect()->to(site_url("panel/usuarios/{$id}"))->with('exito', 'Datos del usuario actualizados.');
    }

    /* =============================== Acciones rápidas =============================== */

    public function asignarRol(int $id): RedirectResponse
    {
        $usuario = $this->buscarOFallar($id);
        $idrol   = (int) $this->request->getPost('idrol');

        if (model(RolModel::class)->find($idrol) === null) {
            return $this->volver()->with('error', 'Seleccione un rol válido.');
        }
        if ($id === (int) session('idusuario')) {
            return $this->volver()->with('error', 'No puede cambiar su propio rol.');
        }
        if ($error = $this->violaUltimoAdmin($usuario, $idrol, (int) $usuario['estado'])) {
            return $this->volver()->with('error', $error);
        }

        model(UsuarioModel::class)->update($id, ['idrol' => $idrol]);
        $rol = model(RolModel::class)->find($idrol)['nombre'];

        return $this->volver()->with('exito', "Se asignó el rol «{$rol}» a {$usuario['nombres']} {$usuario['apellidos']}.");
    }

    public function cambiarEstado(int $id): RedirectResponse
    {
        $usuario = $this->buscarOFallar($id);
        $nuevo   = (int) $usuario['estado'] === 1 && $usuario['email_verificado_en'] !== null ? 0 : 1;

        if ($id === (int) session('idusuario')) {
            return $this->volver()->with('error', 'No puede desactivar su propia cuenta.');
        }
        if ($error = $this->violaUltimoAdmin($usuario, (int) $usuario['idrol'], $nuevo)) {
            return $this->volver()->with('error', $error);
        }

        $datos = ['estado' => $nuevo];
        if ($nuevo === 1 && $usuario['email_verificado_en'] === null) {
            $datos += ['email_verificado_en' => date('Y-m-d H:i:s'), 'token_verificacion' => null, 'token_expira' => null];
        }
        model(UsuarioModel::class)->update($id, $datos);

        $nombre = $usuario['nombres'] . ' ' . $usuario['apellidos'];

        return $this->volver()->with('exito', $nuevo === 1 ? "Cuenta de {$nombre} activada." : "Cuenta de {$nombre} desactivada. Ya no podrá ingresar al sistema.");
    }

    public function reenviarVerificacion(int $id): RedirectResponse
    {
        $usuario = $this->buscarOFallar($id);

        if ($usuario['email_verificado_en'] !== null) {
            return $this->volver()->with('info', 'Este usuario ya confirmó su correo.');
        }

        $token = model(UsuarioModel::class)->nuevoTokenVerificacion($id);

        if (Correo::enviarVerificacion($usuario, $token)) {
            return $this->volver()->with('exito', "Se envió un nuevo enlace de confirmación a {$usuario['email']}.");
        }

        if (ENVIRONMENT === 'development') {
            return $this->volver()->with('info', '[Modo desarrollo] SMTP sin configurar. Enlace de confirmación:')
                ->with('enlace_dev', site_url('registro/verificar/' . $token));
        }

        return $this->volver()->with('error', 'No se pudo enviar el correo. Revise la configuración SMTP.');
    }

    public function eliminar(int $id): RedirectResponse
    {
        $usuario = $this->buscarOFallar($id);

        if ($id === (int) session('idusuario')) {
            return redirect()->to(site_url('panel/usuarios'))->with('error', 'No puede eliminar su propia cuenta.');
        }
        if ($error = $this->violaUltimoAdmin($usuario, 0, 0)) {
            return redirect()->to(site_url('panel/usuarios'))->with('error', $error);
        }

        $tieneVacaciones = db_connect()->table('vacaciones')
            ->groupStart()->where('idusuario', $id)->orWhere('autorizado_por', $id)->groupEnd()
            ->countAllResults() > 0;
        if ($tieneVacaciones) {
            return redirect()->to(site_url("panel/usuarios/{$id}"))
                ->with('error', 'Este usuario tiene vacaciones registradas o autorizadas y no se puede eliminar. Desactívelo en su lugar.');
        }

        model(UsuarioModel::class)->delete($id);

        return redirect()->to(site_url('panel/usuarios'))
            ->with('exito', "Usuario {$usuario['nombres']} {$usuario['apellidos']} eliminado.");
    }

    /* =============================== Utilidades =============================== */

    /**
     * Evita quedarse sin ningún Administrador activo.
     */
    private function violaUltimoAdmin(array $usuario, int $nuevoRol, int $nuevoEstado): ?string
    {
        $esAdminActivo = (int) $usuario['idrol'] === RolModel::ADMINISTRADOR && (int) $usuario['estado'] === 1;
        $dejaDeSerlo   = $nuevoRol !== RolModel::ADMINISTRADOR || $nuevoEstado !== 1;

        if ($esAdminActivo && $dejaDeSerlo && model(UsuarioModel::class)->adminsActivos((int) $usuario['idusuario']) === 0) {
            return 'No se puede realizar el cambio: es el único Administrador activo del sistema.';
        }

        return null;
    }

    private function reglas(?int $id): array
    {
        $letras = '/^[\p{L}][\p{L} .\'-]*$/u';
        $ignora = $id === null ? '' : ",idusuario,{$id}";
        $passReq = $id === null ? 'required' : 'permit_empty';

        return [
            'nombres' => ['label' => 'nombres', 'rules' => ['required', 'min_length[2]', 'max_length[100]', "regex_match[{$letras}]"],
                'errors' => ['required' => 'Los nombres son obligatorios.', 'regex_match' => 'Los nombres solo pueden contener letras.',
                    'min_length' => 'Los nombres deben tener al menos 2 letras.']],
            'apellidos' => ['label' => 'apellidos', 'rules' => ['required', 'min_length[2]', 'max_length[100]', "regex_match[{$letras}]"],
                'errors' => ['required' => 'Los apellidos son obligatorios.', 'regex_match' => 'Los apellidos solo pueden contener letras.',
                    'min_length' => 'Los apellidos deben tener al menos 2 letras.']],
            'ci' => ['label' => 'CI', 'rules' => ['required', 'regex_match[/^[0-9]{5,12}(-?[0-9A-Za-z]{1,3})?$/]', "is_unique[usuarios.ci{$ignora}]"],
                'errors' => ['required' => 'El CI es obligatorio.', 'regex_match' => 'El CI debe tener entre 5 y 12 dígitos (puede incluir complemento).',
                    'is_unique' => 'Ya existe otro usuario con este CI.']],
            'email' => ['label' => 'correo', 'rules' => "required|valid_email|max_length[150]|is_unique[usuarios.email{$ignora}]",
                'errors' => ['required' => 'El correo es obligatorio.', 'valid_email' => 'Ingrese un correo válido.', 'is_unique' => 'Ya existe otro usuario con este correo.']],
            'celular' => ['label' => 'celular', 'rules' => 'required|is_natural_no_zero|min_length[7]|max_length[15]',
                'errors' => ['required' => 'El celular es obligatorio.', 'is_natural_no_zero' => 'El celular solo debe contener números.',
                    'min_length' => 'El celular debe tener al menos 7 dígitos.', 'max_length' => 'El celular no puede tener más de 15 dígitos.']],
            'fecha_ingreso' => ['label' => 'fecha de ingreso', 'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => ['valid_date' => 'La fecha de ingreso no es válida.']],
            'idrol' => ['label' => 'rol', 'rules' => 'required|is_natural_no_zero|is_not_unique[roles.idrol]',
                'errors' => ['required' => 'Seleccione un rol.', 'is_not_unique' => 'El rol seleccionado no existe.', 'is_natural_no_zero' => 'Seleccione un rol.']],
            'estado' => ['label' => 'estado', 'rules' => 'required|in_list[0,1]', 'errors' => ['in_list' => 'Estado no válido.']],
            'idcargo' => ['label' => 'cargo', 'rules' => 'permit_empty|is_natural_no_zero|is_not_unique[cargos.idcargo]',
                'errors' => ['is_not_unique' => 'El cargo seleccionado no existe.', 'is_natural_no_zero' => 'Cargo no válido.']],
            'password' => ['label' => 'contraseña', 'rules' => [$passReq, 'min_length[8]', 'max_length[72]', 'regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]'],
                'errors' => ['required' => 'La contraseña es obligatoria.', 'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
                    'regex_match' => 'La contraseña debe incluir letras y números.']],
            'password_confirm' => ['label' => 'confirmación', 'rules' => 'matches[password]',
                'errors' => ['matches' => 'Las contraseñas no coinciden.']],
        ];
    }

    private function fechaIngresoFutura(): ?string
    {
        $f = (string) $this->request->getPost('fecha_ingreso');

        return $f !== '' && $f > date('Y-m-d') ? 'La fecha de ingreso no puede ser futura.' : null;
    }

    private function datosFormulario(): array
    {
        return [
            'nombres'   => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('nombres'))),
            'apellidos' => trim(preg_replace('/\s+/', ' ', (string) $this->request->getPost('apellidos'))),
            'ci'        => strtoupper(trim((string) $this->request->getPost('ci'))),
            'email'     => strtolower(trim((string) $this->request->getPost('email'))),
            'celular'   => (int) $this->request->getPost('celular'),
            'fecha_ingreso' => ((string) $this->request->getPost('fecha_ingreso')) ?: null,
            'idrol'     => (int) $this->request->getPost('idrol'),
            'idcargo'   => ((int) $this->request->getPost('idcargo')) ?: null,
            'estado'    => (int) $this->request->getPost('estado') === 1 ? 1 : 0,
        ];
    }
}
