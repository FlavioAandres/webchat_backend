<?php

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
        factory(App\User::class, 50)->create();
        new App\User([
            'name'=>'Flavio A. Pareja',
            'email'=>'flv.prj@gmail.com',
            'password'=>\Hash::make('secret'),
        ]);
        new App\User([
            'name'=>'Ivan Cadena Florez',
            'email'=>'ivan@ivan.com',
            'password'=>\Hash::make('secret'),
        ]);
    }
}
