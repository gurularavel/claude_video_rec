<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'        => 'Admin',
            'email'       => 'admin@videocall.az',
            'password'    => bcrypt('admin123'),
            'is_operator' => true,
            'status'      => 'available',
        ]);

        User::factory()->create([
            'name'        => 'Operator 1',
            'email'       => 'operator@videocall.az',
            'password'    => bcrypt('operator123'),
            'is_operator' => true,
            'status'      => 'available',
        ]);
    }
}
