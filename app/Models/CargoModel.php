<?php

namespace App\Models;

use CodeIgniter\Model;

class CargoModel extends Model
{
    protected $table         = 'cargos';
    protected $primaryKey    = 'idcargo';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'descripcion', 'estado'];

    // created_at la llena MySQL (DEFAULT CURRENT_TIMESTAMP)
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    /** Cargos con la cantidad de usuarios asignados. */
    public function listarConConteos(string $q = ''): array
    {
        $u = $this->db->prefixTable('usuarios');
        $this->select("cargos.*, (SELECT COUNT(*) FROM {$u} u WHERE u.idcargo = cargos.idcargo) AS total_usuarios", false)
            ->orderBy('cargos.nombre');

        if (($q = trim($q)) !== '') {
            $this->groupStart()->like('cargos.nombre', $q)->orLike('cargos.descripcion', $q)->groupEnd();
        }

        return $this->findAll();
    }

    /**
     * Cargos para un <select>: los activos más el actual del usuario (aunque esté inactivo).
     */
    public function paraSelect(?int $actual = null): array
    {
        $this->groupStart()->where('estado', 1);
        if ($actual) {
            $this->orWhere('idcargo', $actual);
        }

        return $this->groupEnd()->orderBy('nombre')->findAll();
    }

    public function totalUsuarios(int $idcargo): int
    {
        return $this->db->table('usuarios')->where('idcargo', $idcargo)->countAllResults();
    }

    public function usuariosDe(int $idcargo): array
    {
        return $this->db->table('usuarios u')
            ->select('u.idusuario, u.nombres, u.apellidos, u.email, u.estado, r.nombre AS rol')
            ->join('roles r', 'r.idrol = u.idrol')
            ->where('u.idcargo', $idcargo)
            ->orderBy('u.apellidos')
            ->get()->getResultArray();
    }
}
