<?php

namespace Database\Seeders;

use App\Models\movement_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('movement_types')->insert([
            [
                'name' => 'Transferencia',
                'icon' => 'fa fa-cart-plus',
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => 'Vendido por PDV',
                'icon' => 'fa fa-shopping-basket',
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => 'Compra',
                'icon' => 'fa fa-cart-plus',
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => 'Vendido por Faturação',
                'icon' => 'fa fa-shopping-basket',
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => 'Entrada',
                'icon' => 'fa fa-bar-chart',
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'name' => 'Saida',
                'icon' => 'fa fa-bar-chart',
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ]);
    }
}
