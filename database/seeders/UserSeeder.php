<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
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
