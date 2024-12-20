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
        $firstUser = User::factory()->create([
            'balance' => 500,
            'rating' => 4.5
        ]);

        $secondUser = User::factory()->create([
            'balance' => 1500,
            'rating' => 4.5
        ]);


    }
}
