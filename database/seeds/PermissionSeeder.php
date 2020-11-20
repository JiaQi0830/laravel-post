<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('permissions')->insert([[
            'name' => 'write post',
            'guard_name' => 'web'
        ],[
            'name' => 'edit post',
            'guard_name' => 'web'
        ],[
            'name' => 'like post',
            'guard_name' => 'web'
        ],[
            'name' => 'comment post',
            'guard_name' => 'web'
        ],[
            'name' => 'delete post',
            'guard_name' => 'web'
        ]]);
    }
}
