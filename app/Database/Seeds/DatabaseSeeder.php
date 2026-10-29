<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Uso: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('RolesPermisosSeeder');
        $this->call('AdminSeeder');
    }
}
