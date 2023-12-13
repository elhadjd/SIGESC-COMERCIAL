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
                'name' => 'Faturamento',
                'company_id'=> '1',
                'href'=>'faturacao',
                'image' => 'faturation.png',
            ],
            [
                'name' => 'Definiçoes',
                'company_id'=> '1',
                'href'=>'configuracoes',
                'image' => 'config.png',
            ],
            [
                'name' => 'Funcionarios',
                'company_id'=> '1',
                'href'=>'funcionarios',
                'image' => 'funcionarios.png',
            ],
            [
                'name' => 'Compras',
                'company_id'=> '1',
                'href'=>'compra',
                'image' => 'purchase.svg',
            ],
            [
                'name' => 'Ponto de venda',
                'company_id'=> '1',
                'href'=>'pontodevenda',
                'image' => 'pdv.svg',
            ],
            [
                'name' => 'Gerenciamento de stock',
                'company_id'=> '1',
                'href'=>'Stock',
                'image' => 'stock.png',
            ]
        ]);
    }
}
