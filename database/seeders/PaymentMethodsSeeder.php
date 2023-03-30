<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->insert([
            [
                'name' => 'Numerario',
                'valor' => 0,
            ],
            [
                'name' => 'Transferencia',
                'valor' => 0,
            ],
            [
                'name' => 'Multicaixa',
                'valor' => 0,
            ]
        ]);
    }
}
