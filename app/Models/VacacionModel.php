<?php

namespace App\Models;

use CodeIgniter\Model;

class VacacionModel extends Model
{
    public const ESTADOS = ['pendiente', 'autorizado', 'rechazado', 'cancelado'];

    protected $table         = 'vacaciones';
    protected $primaryKey    = 'idvacacion';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'idusuario', 'fecha_solicitud', 'fecha_inicio', 'fecha_fin', 'dias_habiles', 'motivo',
        'estado', 'autorizado_por', 'fecha_autorizacion', 'observacion_coordinador',
    ];

    // fecha_registro la llena MySQL (DEFAULT CURRENT_TIMESTAMP)
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    /** Base con nombres del solicitante y del autorizador. */
    private function conNombres(): self
    {
        return $this->select("vacaciones.*,
                CONCAT(u.nombres, ' ', u.apellidos) AS solicitante, u.email AS email_solicitante, u.ci, u.idrol,
                u.fecha_ingreso, r.nombre AS rol,
                CONCAT(a.nombres, ' ', a.apellidos) AS autorizador", false)
            ->join('usuarios u', 'u.idusuario = vacaciones.idusuario')
            ->join('roles r', 'r.idrol = u.idrol')
            ->join('usuarios a', 'a.idusuario = vacaciones.autorizado_por', 'left');
    }

    public function detalle(int $id): ?array
    {
        return $this->conNombres()->find($id);
    }

    public function deUsuario(int $idusuario): array
    {
        return $this->conNombres()
            ->where('vacaciones.idusuario', $idusuario)
            ->orderBy('vacaciones.fecha_inicio', 'DESC')
            ->findAll();
    }

    /** ¿El usuario ya tiene vacaciones pendientes o autorizadas que se crucen con el rango? */
    public function seSolapa(int $idusuario, string $inicio, string $fin, ?int $excluir = null): ?array
    {
        $b = $this->where('idusuario', $idusuario)
            ->whereIn('estado', ['pendiente', 'autorizado'])
            ->where('fecha_inicio <=', $fin)
            ->where('fecha_fin >=', $inicio);
        if ($excluir !== null) {
            $b->where('idvacacion !=', $excluir);
        }

        return $b->first();
    }

    /** Otras personas ausentes (autorizadas o pendientes) en el rango. */
    public function ausentesEn(string $inicio, string $fin, int $excluirUsuario): array
    {
        return $this->conNombres()
            ->whereIn('vacaciones.estado', ['pendiente', 'autorizado'])
            ->where('vacaciones.idusuario !=', $excluirUsuario)
            ->where('vacaciones.fecha_inicio <=', $fin)
            ->where('vacaciones.fecha_fin >=', $inicio)
            ->orderBy('vacaciones.fecha_inicio')
            ->findAll();
    }

    /**
     * Solicitudes del personal con filtros (para paginar).
     *
     * @param array{q?: string, estado?: string, desde?: string, hasta?: string} $f
     */
    public function filtrar(array $f): self
    {
        $this->conNombres();

        if (($q = trim((string) ($f['q'] ?? ''))) !== '') {
            $this->groupStart()
                ->like("CONCAT(u.nombres, ' ', u.apellidos)", $q, 'both', null, true)
                ->orLike('u.ci', $q)
                ->orLike('u.email', $q)
            ->groupEnd();
        }
        if (in_array($f['estado'] ?? '', self::ESTADOS, true)) {
            $this->where('vacaciones.estado', $f['estado']);
        }
        if (! empty($f['desde'])) {
            $this->where('vacaciones.fecha_fin >=', $f['desde']);
        }
        if (! empty($f['hasta'])) {
            $this->where('vacaciones.fecha_inicio <=', $f['hasta']);
        }

        return $this->orderBy("FIELD(vacaciones.estado, 'pendiente') DESC, vacaciones.fecha_inicio DESC", '', false);
    }

    /** Pendientes que puede resolver un autorizador (excluye las propias). */
    public function pendientesPara(int $idAutorizador): int
    {
        return $this->where('estado', 'pendiente')->where('idusuario !=', $idAutorizador)->countAllResults();
    }

    /**
     * Resuelve una solicitud solo si sigue pendiente (evita dobles aprobaciones).
     */
    public function resolver(int $id, string $estado, int $autorizador, ?string $observacion): bool
    {
        $this->builder()
            ->where('idvacacion', $id)
            ->where('estado', 'pendiente')
            ->update([
                'estado'                  => $estado,
                'autorizado_por'          => $autorizador,
                'fecha_autorizacion'      => date('Y-m-d H:i:s'),
                'observacion_coordinador' => $observacion,
                'updated_at'              => date('Y-m-d H:i:s'),
            ]);

        return $this->db->affectedRows() === 1;
    }
}
