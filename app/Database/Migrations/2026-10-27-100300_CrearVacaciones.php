<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CrearVacaciones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'idvacacion'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'idusuario'               => ['type' => 'INT', 'unsigned' => true, 'comment' => 'Colaborador que solicita'],
            'fecha_solicitud'         => ['type' => 'DATE'],
            'fecha_inicio'            => ['type' => 'DATE'],
            'fecha_fin'               => ['type' => 'DATE'],
            'dias_habiles'            => ['type' => 'DECIMAL', 'constraint' => '4,1', 'default' => 0, 'comment' => 'Días hábiles solicitados (admite medio día)'],
            'motivo'                  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'estado'                  => ['type' => 'ENUM', 'constraint' => ['pendiente', 'autorizado', 'rechazado', 'cancelado'], 'default' => 'pendiente'],
            'autorizado_por'          => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'comment' => 'Coordinador que autoriza o rechaza'],
            'fecha_autorizacion'      => ['type' => 'DATETIME', 'null' => true],
            'observacion_coordinador' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'fecha_registro'          => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'              => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('idvacacion');
        $this->forge->addKey(['idusuario', 'fecha_inicio'], false, false, 'ix_vacaciones_usuario_inicio');
        $this->forge->addKey('estado', false, false, 'ix_vacaciones_estado');
        $this->forge->addForeignKey('idusuario', 'usuarios', 'idusuario', 'CASCADE', 'RESTRICT', 'fk_vacaciones_usuario');
        $this->forge->addForeignKey('autorizado_por', 'usuarios', 'idusuario', 'CASCADE', 'SET NULL', 'fk_vacaciones_autorizador');
        $this->forge->createTable('vacaciones', true, ['ENGINE' => 'InnoDB', 'COMMENT' => 'Registro de vacaciones']);

        // La fecha final no puede ser anterior a la inicial
        $this->db->query('ALTER TABLE ' . $this->db->prefixTable('vacaciones')
            . ' ADD CONSTRAINT chk_vacaciones_fechas CHECK (fecha_fin >= fecha_inicio)');
    }

    public function down()
    {
        $this->forge->dropTable('vacaciones', true);
    }
}
