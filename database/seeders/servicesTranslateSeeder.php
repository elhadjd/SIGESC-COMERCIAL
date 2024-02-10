<?php

namespace Database\Seeders;

use App\Models\serviceTranslate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class servicesTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service_translates')->insert([
            [
                'service_id'=>1,
                'local'=>'en',
                'translate'=>'Products',
            ],
            [
                'service_id'=>2,
                'local'=>'en',
                'translate'=>'Clients',
            ],
            [
                'service_id'=>3,
                'local'=>'en',
                'translate'=>'Company',
            ],
            [
                'service_id'=>4,
                'local'=>'en',
                'translate'=>'Discounts',
            ],
            [
                'service_id'=>5,
                'local'=>'en',
                'translate'=>'Providers',
            ],
            [
                'service_id'=>6,
                'local'=>'en',
                'translate'=>'Payments',
            ],
            [
                'service_id'=>7,
                'local'=>'en',
                'translate'=>'Workers',
            ],
            [
                'service_id'=>8,
                'local'=>'en',
                'translate'=>'Invoices',
            ],
            [
                'service_id'=>9,
                'local'=>'en',
                'translate'=>'Purchases',
            ],
            [
                'service_id'=>10,
                'local'=>'en',
                'translate'=>'Products transfers',
            ],
            [
                'service_id'=>11,
                'local'=>'en',
                'translate'=>'Stores',
            ],
            [
                'service_id'=>12,
                'local'=>'en',
                'translate'=>'Departments',
            ],
            [
                'service_id'=>13,
                'local'=>'en',
                'translate'=>'Companies',
            ],
            [
                'service_id'=>14,
                'local'=>'en',
                'translate'=>'Users',
            ],
            [
                'service_id'=>15,
                'local'=>'en',
                'translate'=>'POS',
            ],







            [

                'service_id'=>1,
                'local'=>'pt',
                'translate'=>'Produtos',
            ],
            [
                'service_id'=>2,
                'local'=>'pt',
                'translate'=>'Clientes',
            ],
            [
                'service_id'=>3,
                'local'=>'pt',
                'translate'=>'Empresa',
            ],
            [
                'service_id'=>4,
                'local'=>'pt',
                'translate'=>'Disconto',
            ],
            [
                'service_id'=>5,
                'local'=>'pt',
                'translate'=>'Fornecedores',
            ],
            [
                'service_id'=>6,
                'local'=>'pt',
                'translate'=>'Pagamentos',
            ],
            [
                'service_id'=>7,
                'local'=>'pt',
                'translate'=>'Employees',
            ],
            [
                'service_id'=>8,
                'local'=>'pt',
                'translate'=>'Faturas',
            ],
            [
                'service_id'=>9,
                'local'=>'pt',
                'translate'=>'Compras',
            ],
            [
                'service_id'=>10,
                'local'=>'pt',
                'translate'=>'Transferências de produtos',
            ],
            [
                'service_id'=>11,
                'local'=>'pt',
                'translate'=>'Armagens',
            ],
            [
                'service_id'=>12,
                'local'=>'pt',
                'translate'=>'Departamentos',
            ],
            [
                'service_id'=>13,
                'local'=>'pt',
                'translate'=>'Empresa',
            ],
            [
                'service_id'=>14,
                'local'=>'pt',
                'translate'=>'Usuarios',
            ],
            [
                'service_id'=>15,
                'local'=>'pt',
                'translate'=>'POS',
            ],






            [
                'service_id'=>1,
                'local'=>'fr',
                'translate'=>'Produits',
            ],
            [
                'service_id'=>2,
                'local'=>'fr',
                'translate'=>'Clientèle',
            ],
            [
                'service_id'=>3,
                'local'=>'fr',
                'translate'=>'Entreprise',
            ],
            [
                'service_id'=>4,
                'local'=>'fr',
                'translate'=>'Réductions',
            ],
            [
                'service_id'=>5,
                'local'=>'fr',
                'translate'=>'Fournisseurs',
            ],
            [
                'service_id'=>6,
                'local'=>'fr',
                'translate'=>'Paiements',
            ],
            [
                'service_id'=>7,
                'local'=>'pt',
                'translate'=>'Employés',
            ],
            [
                'service_id'=>8,
                'local'=>'fr',
                'translate'=>'Factures',
            ],
            [
                'service_id'=>9,
                'local'=>'fr',
                'translate'=>'Achats',
            ],
            [
                'service_id'=>10,
                'local'=>'fr',
                'translate'=>'Transferts de produits',
            ],
            [
                'service_id'=>11,
                'local'=>'fr',
                'translate'=>'Entrepôts',
            ],
            [
                'service_id'=>12,
                'local'=>'fr',
                'translate'=>'Départements',
            ],
            [
                'service_id'=>13,
                'local'=>'fr',
                'translate'=>'Entreprise',
            ],
            [
                'service_id'=>14,
                'local'=>'fr',
                'translate'=>'Utilisateurs',
            ],
            [
                'service_id'=>15,
                'local'=>'fr',
                'translate'=>'POS',
            ],
            
        ]);
    }
}
