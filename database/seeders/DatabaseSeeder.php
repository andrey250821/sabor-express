<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos base indispensables para que el sistema funcione
     * desde una instalación limpia.
     */
    public function run(): void
    {
        DB::table('roles')->upsert(
            [
                [
                    'id' => 1,
                    'nombre' => 'Administrador',
                ],
                [
                    'id' => 2,
                    'nombre' => 'Cliente',
                ],
                [
                    'id' => 3,
                    'nombre' => 'Delivery',
                ],
                [
                    'id' => 4,
                    'nombre' => 'Cocinero',
                ],
            ],
            ['id'],
            ['nombre']
        );
    }
}
