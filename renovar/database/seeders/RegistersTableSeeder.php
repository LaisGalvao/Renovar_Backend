<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegistersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('tbcadcliente')->insert(
            [
                'nome' => "Admin",
                'email' => "admin@admin",
               // 'email_verified_at' => now(),
                'CPF' => '854.602.832-89',
                'idade'=> '23',
                 'telefone' => '+55 (11) 997628274',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                //'remember_token' => "asdfasdf",
            ]
        );
    }
}
