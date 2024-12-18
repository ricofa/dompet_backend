<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_users')->insert([
            'name' => 'Dev Admin',
            'email' => 'admin@dompet.com',
            'password' => bcrypt('Tes123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
