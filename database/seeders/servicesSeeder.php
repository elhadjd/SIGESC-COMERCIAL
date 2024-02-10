<?php

namespace Database\Seeders;

use App\Models\service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class servicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'name'=>'Products',
            ],
            [
                'name'=>'Clients',
            ],
            [
                'name'=>'Company',
            ],
            [
                'name'=>'Discounts',
            ],
            [
                'name'=>'Providers',
            ],
            [
                'name'=>'Payments',
            ],
            [
                'name'=>'Employees',
            ],
            [
                'name'=>'Invoices',
            ],
            [
                'name'=>'Purchase',
            ],
            [
                'name'=>'Products transfers',
            ],
            [
                'name'=>'Stores',
            ],
            [
                'name'=>'Departments',
            ],

            [
                'name'=>'Companies',
            ],

            [
                'name'=>'Users',
            ],

            [
                'name'=>'POS',
            ]
        ]);
    }
}
