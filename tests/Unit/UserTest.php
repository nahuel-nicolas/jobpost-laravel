<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    public function test_user_creation(): void
    {
        $user = User::create([
            'name' => 'Guituri',
            'email' => 'guituri@gmail.com',
            'password' => 'password123',
        ]);
        $this->assertModelExists($user);
        $this->assertDatabaseHas('users', [
            'email' => 'guituri@gmail.com'
        ]);
    }

    public function test_users_seed(): void
    {
        $this->assertDatabaseCount('users', 0);
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 3);
    }
}
