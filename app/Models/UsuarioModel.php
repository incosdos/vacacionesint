<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table         = 'usuarios';
    protected $primaryKey    = 'idusuario';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nombres', 'apellidos', 'ci', 'email', 'celular', 'fecha_ingreso', 'idrol', 'idcargo', 'password', 'estado',
        'email_verificado_en', 'token_verificacion', 'token_expira', 'ultimo_acceso',
    ];

    // fechaderegistro la llena MySQL (DEFAULT CURRENT_TIMESTAMP)
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /** Cifra la contraseña si viene en texto plano. */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && password_get_info($data['data']['password'])['algo'] === null) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /** Busca por correo o por CI, junto con el nombre del rol. */
    public function buscarParaLogin(string $usuario): ?array
    {
        return $this->select('usuarios.*, roles.nombre AS rol')
            ->join('roles', 'roles.idrol = usuarios.idrol')
            ->groupStart()
                ->where('usuarios.email', $usuario)
                ->orWhere('usuarios.ci', $usuario)
            ->groupEnd()
            ->first();
    }

    public function buscarPorToken(string $tokenPlano): ?array
    {
        return $this->where('token_verificacion', hash('sha256', $tokenPlano))->first();
    }

    /**
     * Genera un token nuevo de verificación, guarda su hash y devuelve el token plano.
     */
    public function nuevoTokenVerificacion(int $idusuario, int $horasValidez = 24): string
    {
        $token = bin2hex(random_bytes(32));

        $this->update($idusuario, [
            'token_verificacion' => hash('sha256', $token),
            'token_expira'       => date('Y-m-d H:i:s', strtotime("+{$horasValidez} hours")),
        ]);

        return $token;
    }

    /**
     * Aplica búsqueda y filtros (para paginar después).
     *
     * @param array{q?: string, rol?: string|int, estado?: string} $f
     */
    public function filtrar(array $f): self
    {
        $this->select('usuarios.*, roles.nombre AS rol, cargos.nombre AS cargo')
            ->join('roles', 'roles.idrol = usuarios.idrol')
            ->join('cargos', 'cargos.idcargo = usuarios.idcargo', 'left');

        $q = trim((string) ($f['q'] ?? ''));
        if ($q !== '') {
            $this->groupStart()
                ->like("CONCAT(usuarios.nombres, ' ', usuarios.apellidos)", $q, 'both', null, true)
                ->orLike('usuarios.ci', $q)
                ->orLike('usuarios.email', $q)
                ->orLike('usuarios.celular', $q)
            ->groupEnd();
        }

        if (! empty($f['rol'])) {
            $this->where('usuarios.idrol', (int) $f['rol']);
        }

        if (($f['cargo'] ?? '') === 'sin') {
            $this->where('usuarios.idcargo', null);
        } elseif (! empty($f['cargo'])) {
            $this->where('usuarios.idcargo', (int) $f['cargo']);
        }

        switch ($f['estado'] ?? '') {
            case 'activo':
                $this->where('usuarios.estado', 1)->where('usuarios.email_verificado_en IS NOT NULL');
                break;
            case 'inactivo':
                $this->where('usuarios.estado', 0)->where('usuarios.email_verificado_en IS NOT NULL');
                break;
            case 'pendiente':
                $this->where('usuarios.email_verificado_en IS NULL');
                break;
        }

        return $this->orderBy('usuarios.fechaderegistro', 'DESC');
    }

    /** Totales para los indicadores del listado. */
    public function estadisticas(): array
    {
        $fila = $this->db->table('usuarios')->select('
                COUNT(*) AS total,
                SUM(estado = 1 AND email_verificado_en IS NOT NULL) AS activos,
                SUM(estado = 0 AND email_verificado_en IS NOT NULL) AS inactivos,
                SUM(email_verificado_en IS NULL) AS pendientes,
                SUM(idrol = ' . RolModel::INVITADO . ' AND estado = 1) AS invitados', false)
            ->get()->getRowArray();

        return array_map('intval', $fila ?? []);
    }

    /** Administradores activos, opcionalmente excluyendo a uno. */
    public function adminsActivos(?int $excluir = null): int
    {
        $b = $this->db->table('usuarios')
            ->where('idrol', RolModel::ADMINISTRADOR)
            ->where('estado', 1)
            ->where('email_verificado_en IS NOT NULL');
        if ($excluir !== null) {
            $b->where('idusuario !=', $excluir);
        }

        return $b->countAllResults();
    }

    public function conRol(int $id): ?array
    {
        return $this->select('usuarios.*, roles.nombre AS rol, cargos.nombre AS cargo')
            ->join('roles', 'roles.idrol = usuarios.idrol')
            ->join('cargos', 'cargos.idcargo = usuarios.idcargo', 'left')
            ->find($id);
    }

    /**
     * Usuarios activos que pueden autorizar vacaciones (para notificarles).
     */
    public function autorizadores(?int $excluir = null): array
    {
        $b = $this->select('usuarios.idusuario, usuarios.nombres, usuarios.apellidos, usuarios.email')
            ->join('rol_permiso rp', 'rp.idrol = usuarios.idrol')
            ->join('permisos p', 'p.idpermiso = rp.idpermiso')
            ->where('p.clave', 'vacaciones.autorizar')
            ->where('usuarios.estado', 1)
            ->where('usuarios.email_verificado_en IS NOT NULL');
        if ($excluir !== null) {
            $b->where('usuarios.idusuario !=', $excluir);
        }

        return $b->findAll();
    }

    public function marcarVerificado(int $idusuario): bool
    {
        return $this->update($idusuario, [
            'email_verificado_en' => date('Y-m-d H:i:s'),
            'estado'              => 1,
            'token_verificacion'  => null,
            'token_expira'        => null,
        ]);
    }
}
