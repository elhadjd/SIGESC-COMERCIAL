<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new \Database\Seeders\RoleSeeder())->run();
        (new \Database\Seeders\MovementTypeSeeder())->run();
        (new \Database\Seeders\companySeeder())->run();
        (new \Database\Seeders\AppSeeder())->run();
        (new \Database\Seeders\LicenseSeeder())->run();
        (new \Database\Seeders\PaymentMethodsSeeder())->run();
        (new \Database\Seeders\ArmagemSeeder())->run();
        (new \Database\Seeders\UserSeeder())->run();
        (new \Database\Seeders\caixaSeeder())->run();
        (new \Database\Seeders\productType())->run();
        (new \Database\Seeders\TypeOperationSeeder())->run();
        (new \Database\Seeders\typeLicense)->run();
        (new \Database\Seeders\activity_type)->run();
        (new \Database\Seeders\appsTranslateSeeder)->run();
        (new \Database\Seeders\mthodsPaymentTranslateSeeder)->run();
        (new \Database\Seeders\operationTypeCaixaTranslateSeeder)->run();
        (new \Database\Seeders\typeMovementTranslateSeeder)->run();
    }
}
