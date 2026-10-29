<?php

namespace App\Models;

use App\Libraries\CalculoVacaciones;
use CodeIgniter\Model;

class FeriadoModel extends Model
{
    protected $table         = 'feriados';
    protected $primaryKey    = 'idferiado';
    protected $returnType    = 'array';
    protected $allowedFields = ['fecha', 'nombre', 'tipo', 'observacion', 'registrado_por'];

    // created_at la llena MySQL (DEFAULT CURRENT_TIMESTAMP)
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    public function delAnio(int $anio, string $tipo = ''): array
    {
        $this->select("feriados.*, CONCAT(u.nombres, ' ', u.apellidos) AS registrado_nombre", false)
            ->join('usuarios u', 'u.idusuario = feriados.registrado_por', 'left')
            ->where('feriados.fecha >=', "{$anio}-01-01")
            ->where('feriados.fecha <=', "{$anio}-12-31")
            ->orderBy('feriados.fecha');
        if (array_key_exists($tipo, CalculoVacaciones::TIPOS)) {
            $this->where('feriados.tipo', $tipo);
        }

        return $this->findAll();
    }

    /** Años con feriados registrados, más el actual y el siguiente. */
    public function anios(): array
    {
        $filas = $this->db->table('feriados')->select('DISTINCT YEAR(fecha) AS anio', false)->get()->getResultArray();
        $anios = array_map('intval', array_column($filas, 'anio'));
        $anios[] = (int) date('Y');
        $anios[] = (int) date('Y') + 1;
        $anios = array_values(array_unique($anios));
        sort($anios);

        return $anios;
    }

    /**
     * Vuelve a calcular los días hábiles de las vacaciones pendientes o autorizadas
     * que incluyen alguna de las fechas indicadas (porque un feriado se agregó, cambió o quitó).
     *
     * @param list<string> $fechas
     *
     * @return int Solicitudes cuyo número de días cambió
     */
    public function recalcularVacaciones(array $fechas): int
    {
        $fechas = array_values(array_filter(array_unique($fechas)));
        if ($fechas === []) {
            return 0;
        }

        $b = $this->db->table('vacaciones')
            ->select('idvacacion, fecha_inicio, fecha_fin, dias_habiles')
            ->whereIn('estado', ['pendiente', 'autorizado'])
            ->groupStart();
        foreach ($fechas as $f) {
            $b->orGroupStart()->where('fecha_inicio <=', $f)->where('fecha_fin >=', $f)->groupEnd();
        }
        $vacaciones = $b->groupEnd()->get()->getResultArray();

        $calc      = new CalculoVacaciones();
        $cambiadas = 0;
        foreach ($vacaciones as $v) {
            $medio = (float) $v['dias_habiles'] === 0.5 && $v['fecha_inicio'] === $v['fecha_fin'];
            $dias  = $calc->diasHabiles($v['fecha_inicio'], $v['fecha_fin'], $medio);
            if ($dias !== (float) $v['dias_habiles']) {
                $this->db->table('vacaciones')->where('idvacacion', $v['idvacacion'])
                    ->update(['dias_habiles' => $dias, 'updated_at' => date('Y-m-d H:i:s')]);
                $cambiadas++;
            }
        }

        return $cambiadas;
    }
}
