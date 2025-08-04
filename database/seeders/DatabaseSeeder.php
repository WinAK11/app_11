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
        ]);

        // User::factory(10)->create();

        $user = User::factory()->make([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'mobile' => '1234567890',
            'password' => Hash::make('12345678'),
        ]);

        $user->usertype = 'ADM';
        $user->save();
    }
}
