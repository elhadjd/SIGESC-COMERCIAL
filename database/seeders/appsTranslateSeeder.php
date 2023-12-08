<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class appsTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('apps_translates')->insert([
            [
                'translate' => 'Faturamento',
                'local' => 'pt',
                'app_id'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Facturation',
                'local' => 'fr',
                'app_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Billing',
                'local' => 'en',
                'app_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],


            [
                'translate' => 'Definiçoes',
                'local' => 'pt',
                'app_id'=>2,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Définitions',
                'local' => 'fr',
                'app_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Definitions',
                'local' => 'en',
                'app_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'translate' => 'Funcionarios',
                'local' => 'pt',
                'app_id'=>3,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Employés',
                'local' => 'fr',
                'app_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Employees',
                'local' => 'en',
                'app_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],


            [
                'translate' => 'Compras',
                'local' => 'pt',
                'app_id'=>4,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Achat',
                'local' => 'fr',
                'app_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Purchase',
                'local' => 'en',
                'app_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'translate' => 'Ponto de venda',
                'local' => 'pt',
                'app_id'=>5,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Point de vente',
                'local' => 'fr',
                'app_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Point of sale',
                'local' => 'en',
                'app_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'translate' => 'Gerenciamento de stock',
                'local' => 'pt',
                'app_id'=>6,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Gestion de stock',
                'local' => 'fr',
                'app_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Stock management',
                'local' => 'en',
                'app_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
