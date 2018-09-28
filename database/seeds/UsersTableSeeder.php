<?php

use App\User;
use App\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [   
            'first_name' => 'Daniela',
            'last_name' => 'Sánchez',
            'email' => 'coordinacion@proyectosigue.com.mx',
            'interests' => 'Proyecto Sigue',
            'profile_image' => '',  
            'password' => Hash::make('123456')
        ];
        $user_instance = User::create($user);
        $user_instance->roles()->attach(Role::whereDescription('Administrador')->first()->id);
    }
}
