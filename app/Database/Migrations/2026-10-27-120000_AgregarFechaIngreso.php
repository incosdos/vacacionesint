<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Fecha de ingreso del empleado (para calcular días de vacación por antigüedad). */
class AgregarFechaIngreso extends Migration
{
    public function up()
    {
        $this->forge->addColumn('usuarios', [
            'fecha_ingreso' => ['type' => 'DATE', 'null' => true, 'after' => 'celular', 'comment' => 'Fecha de ingreso a la empresa'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('usuarios', 'fecha_ingreso');
    }
}
