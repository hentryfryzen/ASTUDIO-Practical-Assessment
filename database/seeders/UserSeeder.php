<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'first_name' => 'Hentry',
                'last_name' => 'Fryzen',
                'email' => 'hentryfryzen@example.com',
                'password' => Hash::make('password'),
            ],
        ]);

        User::create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
            'password'   => Hash::make('password'),
        ]);
    }
}
