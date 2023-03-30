<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('operation_caixa_types')->insert([
            [
                'name' => 'Gasto'
            ],
            [
                'name' => 'Saida'
            ],
            [
                'name' => 'Entrada'
            ]
        ]);
    }
}