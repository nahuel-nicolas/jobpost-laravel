<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobPost;
use App\Models\User;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'user1',
            'email' => 'user1@email.com',
            'password' => 'user1password'
        ]);
        $user2 = User::factory()->create([
            'name' => 'user2',
            'email' => 'user2@email.com',
            'password' => 'user2password'
        ]);
        JobPost::factory(3)->create([
            'user_id' => $user1->id
        ]);
        JobPost::factory(4)->create([
            'user_id' => $user2->id
        ]);
    }
}
