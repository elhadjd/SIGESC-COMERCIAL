<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
              'name'=>'Admin',
              'created_at' => now(),
              'updated_at' => now()
            ],
            [
              'name'=>'User',
              'created_at' => now(),
              'updated_at' => now()
            ],
            [
                'name'=>'Manager',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        DB::table('roles_translates')->insert([
            [
              'translate' => 'Admin',
              'local'=>'en',
              'role_id'=>1,
              'created_at' => now(),
              'updated_at' => now()
            ],
            [
              'translate' => 'User',
              'local'=>'en',
              'role_id'=>2,
              'created_at' => now(),
              'updated_at' => now()
            ],
            [
                'translate' => 'Manager',
                'local'=>'en',
                'role_id'=>3,
                'created_at' => now(),
                'updated_at' => now()
            ],


            [
                'translate' => 'Administrador',
                'local'=>'pt',
                'role_id'=>1,
                'created_at' => now(),
                'updated_at' => now()
              ],
              [
                'translate' => 'Usuario',
                'local'=>'pt',
                'role_id'=>2,
                'created_at' => now(),
                'updated_at' => now()
              ],
              [
                  'translate' => 'Gerente',
                  'local'=>'pt',
                  'role_id'=>3,
                  'created_at' => now(),
                  'updated_at' => now()
              ],

              [
                'translate' => 'Administrateur',
                'local'=>'fr',
                'role_id'=>1,
                'created_at' => now(),
                'updated_at' => now()
              ],
              [
                'translate' => 'Utilisateur',
                'local'=>'fr',
                'role_id'=>2,
                'created_at' => now(),
                'updated_at' => now()
              ],
              [
                  'translate' => 'Directeur',
                  'local'=>'fr',
                  'role_id'=>3,
                  'created_at' => now(),
                  'updated_at' => now()
              ],

      ]);
    }
}
