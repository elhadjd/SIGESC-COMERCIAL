<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class operationTypeCaixaTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('operation_caixa_translates')->insert([
            [
                'translate' => 'Dispesas',
                'local' => 'pt',
                'operation_id'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Dépenses',
                'local' => 'fr',
                'operation_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Expenses',
                'local' => 'en',
                'operation_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Saida',
                'local' => 'pt',
                'operation_id'=>2,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Sortie',
                'local' => 'fr',
                'operation_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Exit',
                'local' => 'en',
                'operation_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Entrada',
                'local' => 'pt',
                'operation_id'=>3,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Entrée',
                'local' => 'fr',
                'operation_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Input',
                'local' => 'en',
                'operation_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
