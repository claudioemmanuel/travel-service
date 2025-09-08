<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'User2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);

        TravelRequest::factory(3)->requested()->create(['user_id' => $user->id]);
        TravelRequest::factory(2)->approved()->create(['user_id' => $user->id]);
        TravelRequest::factory(1)->cancelled()->create(['user_id' => $user->id]);

        TravelRequest::factory(2)->requested()->create(['user_id' => $user2->id]);
        TravelRequest::factory(3)->approved()->create(['user_id' => $user2->id]);
    }
}
