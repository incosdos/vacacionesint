<?php

namespace App\Models;

use CodeIgniter\Model;

class RolModel extends Model
{
    public const ADMINISTRADOR = 1;
    public const COORDINADOR   = 2;
    public const COLABORADOR   = 3;
    public const INVITADO      = 4;

    /** Roles base del sistema: no se pueden eliminar. */
    public const PROTEGIDOS = [self::ADMINISTRADOR, self::COORDINADOR, self::COLABORADOR, self::INVITADO];

    protected $table         = 'roles';
    protected $primaryKey    = 'idrol';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'descripcion'];

    public static function esProtegido(int $idrol): bool
    {
        return in_array($idrol, self::PROTEGIDOS, true);
    }

    /** Roles con la cantidad de usuarios y de permisos de cada uno. */
    public function listarConConteos(): array
    {
        $u  = $this->db->prefixTable('usuarios');
        $rp = $this->db->prefixTable('rol_permiso');

        return $this->select("roles.*,
                (SELECT COUNT(*) FROM {$u} u WHERE u.idrol = roles.idrol) AS total_usuarios,
                (SELECT COUNT(*) FROM {$rp} rp WHERE rp.idrol = roles.idrol) AS total_permisos", false)
            ->orderBy('roles.idrol')
            ->findAll();
    }

    public function totalUsuarios(int $idrol): int
    {
        return $this->db->table('usuarios')->where('idrol', $idrol)->countAllResults();
    }

    /** @return list<int> */
    public function idsPermisos(int $idrol): array
    {
        $filas = $this->db->table('rol_permiso')->select('idpermiso')->where('idrol', $idrol)->get()->getResultArray();

        return array_map('intval', array_column($filas, 'idpermiso'));
    }

    /**
     * Mapa idrol => [idpermiso, ...] de todos los roles.
     *
     * @return array<int, list<int>>
     */
    public function mapaPermisos(): array
    {
        $mapa = [];
        foreach ($this->db->table('rol_permiso')->get()->getResultArray() as $f) {
            $mapa[(int) $f['idrol']][] = (int) $f['idpermiso'];
        }

        return $mapa;
    }

    /**
     * Reemplaza los permisos de un rol (en una transacción).
     * El Administrador siempre conserva todos los permisos.
     *
     * @param list<int|string> $idsPermisos
     */
    public function asignarPermisos(int $idrol, array $idsPermisos): bool
    {
        $validos = array_map('intval', array_column(
            $this->db->table('permisos')->select('idpermiso')->get()->getResultArray(),
            'idpermiso'
        ));

        $ids = $idrol === self::ADMINISTRADOR
            ? $validos
            : array_values(array_intersect($validos, array_map('intval', $idsPermisos)));

        $this->db->transStart();
        $this->db->table('rol_permiso')->where('idrol', $idrol)->delete();
        if ($ids !== []) {
            $this->db->table('rol_permiso')->insertBatch(
                array_map(static fn (int $id) => ['idrol' => $idrol, 'idpermiso' => $id], $ids)
            );
        }
        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
