<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin_test@mail.com',
            'password' => Hash::make('password')
        ]);

        $user->assignRole('admin');
        $user->givePermissionTo('write post');
        $user->givePermissionTo('edit post');
        $user->givePermissionTo('delete post');
    }
}
