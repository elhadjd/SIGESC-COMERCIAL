<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class permissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'service_id'=>1,
                'name'=>'Create',
            ],
            [
                'service_id'=>1,
                'name'=>'Edit cost price',
            ],
            [
                'service_id'=>1,
                'name'=>'Stock entry',
            ],
            [
                'service_id'=>1,
                'name'=>'Out of stock',
            ],
            [
                'service_id'=>1,
                'name'=>'Edit cost sale',
            ],
            [
                'service_id'=>1,
                'name'=>'Delete',
            ],
            [
                'service_id'=>1,
                'name'=>'Archive',
            ],






            [
                'service_id'=>2,
                'name'=>'Create',
            ],
            [
                'service_id'=>2,
                'name'=>'Edit',
            ],
            [
                'service_id'=>2,
                'name'=>'Delete',
            ],
            [
                'service_id'=>2,
                'name'=>'Archive',
            ],





            [
                'service_id'=>3,
                'name'=>'Edit',
            ],




            [
                'service_id'=>4,
                'name'=>'Create',
            ],
            [
                'service_id'=>4,
                'name'=>'Edit',
            ],
            [
                'service_id'=>4,
                'name'=>'Active',
            ],
            [
                'service_id'=>4,
                'name'=>'Delete',
            ],





            [
                'service_id'=>5,
                'name'=>'Create',
            ],
            [
                'service_id'=>5,
                'name'=>'Edit',
            ],
            [
                'service_id'=>5,
                'name'=>'Active',
            ],
            [
                'service_id'=>5,
                'name'=>'Delete',
            ],





            [
                'service_id'=>6,
                'name'=>'Create',
            ],
            [
                'service_id'=>6,
                'name'=>'Edit',
            ],





            [
                'service_id'=>7,
                'name'=>'Create',
            ],
            [
                'service_id'=>7,
                'name'=>'Edit',
            ],
            [
                'service_id'=>7,
                'name'=>'Active',
            ],
            [
                'service_id'=>7,
                'name'=>'Delete',
            ],





            [
                'service_id'=>8,
                'name'=>'Create',
            ],
            [
                'service_id'=>8,
                'name'=>'Print',
            ],
            [
                'service_id'=>8,
                'name'=>'Cancel',
            ],
            [
                'service_id'=>8,
                'name'=>'Edit',
            ],




            [
                'service_id'=>9,
                'name'=>'Create',
            ],
            [
                'service_id'=>9,
                'name'=>'Edit',
            ],
            [
                'service_id'=>9,
                'name'=>'Print',
            ],
            [
                'service_id'=>9,
                'name'=>'Cancel',
            ],




            [
                'service_id'=>10,
                'name'=>'Create',
            ],
            [
                'service_id'=>10,
                'name'=>'Edit',
            ],
            [
                'service_id'=>10,
                'name'=>'Print',
            ],
            [
                'service_id'=>10,
                'name'=>'Cancel',
            ],




            [
                'service_id'=>11,
                'name'=>'Create',
            ],
            [
                'service_id'=>11,
                'name'=>'Edit',
            ],
            [
                'service_id'=>11,
                'name'=>'Archive',
            ],
            [
                'service_id'=>11,
                'name'=>'Delete',
            ],




            [
                'service_id'=>12,
                'name'=>'Create',
            ],
            [
                'service_id'=>12,
                'name'=>'Edit',
            ],
            [
                'service_id'=>12,
                'name'=>'Archive',
            ],
            [
                'service_id'=>12,
                'name'=>'Delete',
            ],



            [
                'service_id'=>13,
                'name'=>'Create',
            ],
            [
                'service_id'=>13,
                'name'=>'Edit',
            ],



            [
                'service_id'=>14,
                'name'=>'Create',
            ],
            [
                'service_id'=>14,
                'name'=>'Edit',
            ],
            [
                'service_id'=>14,
                'name'=>'Archive',
            ],
            [
                'service_id'=>14,
                'name'=>'Delete',
            ],




            [
                'service_id'=>15,
                'name'=>'Open session',
            ],
            [
                'service_id'=>15,
                'name'=>'Close session',
            ],
            [
                'service_id'=>15,
                'name'=>'Edit session',
            ],
            [
                'service_id'=>15,
                'name'=>'Can move money',
            ],
            [
                'service_id'=>15,
                'name'=>'Reprint Point of Sale receipt',
            ],
            [
                'service_id'=>15,
                'name'=>'Print complete information company',
            ],
            [
                'service_id'=>15,
                'name'=>'Can edit price of product',
            ],
            [
                'service_id'=>15,
                'name'=>'Cancel invoice',
            ],
            [
                'service_id'=>15,
                'name'=>'Can make a cash deposit',
            ],
            [
                'service_id'=>15,
                'name'=>'Can withdraw money.',
            ],
        ]);
    }
}
