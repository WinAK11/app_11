<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MonthSeeder::class,
            CategorySeeder::class,
            AuthorSeeder::class,
            ProductSeeder::class,
            SlideSeeder::class
        ]);

        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'mobile' => '1234567890',
            'password' => Hash::make('12345678'),
            'usertype' => 'ADM',
            'email_verified_at' => now(), // 👈 add this
        ]);

        User::create([
            'name' => 'user',
            'email' => 'user@user.com',
            'mobile' => '1234567891',
            'password' => Hash::make('12345678'),
            'usertype' => 'USR',
            'email_verified_at' => now(), // 👈 add this too
        ]);

    }
}
