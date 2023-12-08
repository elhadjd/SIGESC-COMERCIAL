<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class typeMovementTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('type_movement_translates')->insert([
            [
                'translate' => 'Transferencia',
                'local' => 'pt',
                'type_movement_id'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Transfér',
                'local' => 'fr',
                'type_movement_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Transfer',
                'local' => 'en',
                'type_movement_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],


            [
                'translate' => 'Vendido por PDV',
                'local' => 'pt',
                'type_movement_id'=>2,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Vendu par PDV',
                'local' => 'fr',
                'type_movement_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Sold by PDV',
                'local' => 'en',
                'type_movement_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],


            [
                'translate' => 'Compra',
                'local' => 'pt',
                'type_movement_id'=>3,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Achat',
                'local' => 'fr',
                'type_movement_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Purchase',
                'local' => 'en',
                'type_movement_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'translate' => 'Vendido por Faturação',
                'local' => 'pt',
                'type_movement_id'=>4,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Vendu sur facture',
                'local' => 'fr',
                'type_movement_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Sold by Invoice',
                'local' => 'en',
                'type_movement_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],


            [
                'translate' => 'Entrada',
                'local' => 'pt',
                'type_movement_id'=>5,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Entrée',
                'local' => 'fr',
                'type_movement_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Entered',
                'local' => 'en',
                'type_movement_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'translate' => 'Saida',
                'local' => 'pt',
                'type_movement_id'=>6,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Sortie',
                'local' => 'fr',
                'type_movement_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Output',
                'local' => 'en',
                'type_movement_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
