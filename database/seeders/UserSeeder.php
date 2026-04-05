<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'adminp',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 1,
            'parent_id' => null
        ]
    );

    \App\Models\User::updateOrCreate(
        ['email' => 'superadmin@example.com'],
        [
            'name' => 'superadmin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 2,
            'parent_id' => null
        ]
    );
}
}
