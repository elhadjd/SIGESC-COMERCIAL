<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('apps')->insert([
            [
                'name' => 'faturacao',
                'company_id'=> '1',
                'image' => 'faturation.png',
            ],

            [
                'name' => 'configuracoes',
                'company_id'=> '1',
                'image' => 'config.png',
            ],
            [
                'name' => 'funcionarios',
                'company_id'=> '1',
                'image' => 'funcionarios.png',
            ],
            [
                'name' => 'compra',
                'company_id'=> '1',
                'image' => 'purchur.jpg',
            ],
            [
                'name' => 'pontodevenda',
                'company_id'=> '1',
                'image' => 'point-of-sale.png',
            ],
            [
                'name' => 'Stock',
                'company_id'=> '1',
                'image' => 'stock.png',
            ]
        ]);
    }
}
