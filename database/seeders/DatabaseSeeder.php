<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->count(1000)
            ->has(Transaction::factory()->sequence(
                ['status' => 'pending'],
                ['status' => 'completed'],
                ['status' => 'failed'],
            )->count(10))
            ->create();
    }
}
