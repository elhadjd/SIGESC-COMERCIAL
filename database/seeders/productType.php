<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class productType extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_types')->insert([
            [
                "name" => 'Artigo armagenavel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "name" => 'Serviço',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
