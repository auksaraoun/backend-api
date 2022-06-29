<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::firstOrCreate(
            [
                'id' => 1
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@mail.com',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => bcrypt('123456')
            ]
        );
    }
}
