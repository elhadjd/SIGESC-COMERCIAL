<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class mthodsPaymentTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('methods_payment_translates')->insert([
            [
                'translate' => 'Numerario',
                'local' => 'pt',
                'methods_id'=>1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Espèces',
                'local' => 'fr',
                'methods_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Cash',
                'local' => 'en',
                'methods_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Transferencia',
                'local' => 'pt',
                'methods_id'=>2,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Transfert',
                'local' => 'fr',
                'methods_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Transfer',
                'local' => 'en',
                'methods_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Multicaixa',
                'local' => 'pt',
                'methods_id'=>3,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'translate' => 'Carte',
                'local' => 'fr',
                'methods_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'translate' => 'Card',
                'local' => 'en',
                'methods_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
