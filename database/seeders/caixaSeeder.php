<?php

namespace Database\Seeders;

use App\Models\caixa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class caixaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        caixa::create([
            'name' => 'Shop',
            'user_id' => 1,
            'company_id' => 1,
            'password' => '$2y$10$abLBYBFqKz/AcHPRZRBu/uMrFnRjcCEQjKoEd3d4tIlRmeKAWz1zm'
        ]);
    }
}
