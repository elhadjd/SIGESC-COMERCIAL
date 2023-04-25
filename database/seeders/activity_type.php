<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class activity_type extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('activity_types')->insert([
            [
                'name'=>'Loja',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Shopping',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Restourante',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Farmacia',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Padaria',
                'created_at'=>now(),
                'updated_at'=>now(),
            ],
            [
                'name'=>'Salão de beleza',
                'created_at'=>now(),
                'updated_at'=>now(),
            ]
        ]);
    }
}
