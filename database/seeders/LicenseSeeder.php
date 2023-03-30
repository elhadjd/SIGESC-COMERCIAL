<?php

namespace Database\Seeders;

use App\Models\license;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        license::create([
            'company_id' => '1',
            'from' => now(),
            'to' => now(),
        ]);
    }
}
