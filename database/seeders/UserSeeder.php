<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'surname' => 'Administrador do sistema',
                'email' => 'admin@gmail.com',
                'armagen_id' => 1,
                'company_id' => '1',
                'image'=> '4ezVrI5czQlWv0Pg.jpeg',
                'name' => 'Administrador do sistema',
                'password' => '$2y$10$EtOByse9f.5QqFpxnPm/HeiTrWEnH5jngb5KnDr7CXOWtuFEl19mi'
            ]
        ]);
    }
}
