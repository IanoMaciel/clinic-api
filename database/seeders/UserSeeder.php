<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();

        $user->create([
            'first_name' => 'Administrador',
            'last_name' => 'do Sistema',
            'is_admin' => 1,
            'is_common' => 0,
            'is_esp' => 0,
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
