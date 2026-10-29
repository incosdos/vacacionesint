<?php

namespace App\Database\Migrations;

use App\Libraries\CalculoVacaciones;
use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Feriados y días no laborables administrables (antes se calculaban por código).
 * Carga los feriados de 2026 y 2027 y agrega el permiso feriados.gestionar (solo Administrador).
 */
class CrearFeriados extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idferiado'      => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'fecha'          => ['type' => 'DATE'],
            'nombre'         => ['type' => 'VARCHAR', 'constraint' => 120],
            'tipo'           => ['type' => 'ENUM', 'constraint' => ['nacional', 'departamental', 'no_laborable'], 'default' => 'nacional'],
            'observacion'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'comment' => 'Decreto o motivo'],
            'registrado_por' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('idferiado');
        $this->forge->addUniqueKey('fecha', 'uq_feriados_fecha');
        $this->forge->addForeignKey('registrado_por', 'usuarios', 'idusuario', 'CASCADE', 'SET NULL', 'fk_feriados_usuario');
        $this->forge->createTable('feriados', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Feriados y días no laborables']);

        // Carga inicial: calendario boliviano de 2026 (con el DS 5521) y 2027
        $calc = new CalculoVacaciones();
        foreach ([2026, 2027] as $anio) {
            foreach ($calc->propuestaAnio($anio) as $f) {
                $this->db->table('feriados')->insert($f + [
                    'observacion' => $anio === 2026 && str_contains($f['nombre'], '(') ? 'Decreto Supremo N° 5521' : null,
                ]);
            }
        }

        // Permiso (si los roles ya existen; en instalación nueva lo crea el seeder)
        if ($this->db->table('roles')->where('idrol', 1)->countAllResults() > 0
            && $this->db->table('permisos')->where('clave', 'feriados.gestionar')->countAllResults() === 0) {
            $this->db->table('permisos')->insert([
                'clave'       => 'feriados.gestionar',
                'descripcion' => 'Administrar feriados y días no laborables',
            ]);
            $this->db->table('rol_permiso')->insert(['idrol' => 1, 'idpermiso' => $this->db->insertID()]);
        }
    }

    public function down()
    {
        $this->db->table('permisos')->where('clave', 'feriados.gestionar')->delete();
        $this->forge->dropTable('feriados', true);
    }
}
