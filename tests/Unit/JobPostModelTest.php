<?php

namespace Tests\Unit;

use App\Models\JobPost;
use App\Models\User;
use Database\Seeders\JobPostSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class JobPostModelTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    public function test_job_post_creation(): void
    {
        $user = User::factory()->create();
        $jobpost = JobPost::create([
            'title' => 'mytitle',
            'company' => 'mycompany',
            'location' => 'mylocation',
            'website' => 'http://google.com',
            'email' => 'myemail@gmail.com',
            'description' => 'mydescription',
            'tags' => 'react, node, django, laravel',
            'user_id' => $user->id,
        ]);

        $this->assertModelExists($jobpost);
        $this->assertDatabaseHas('job_posts', [
            'title' => 'mytitle'
        ]);
    }

    public function test_job_post_seed(): void
    {
        $this->assertDatabaseCount('job_posts', 0);
        $this->seed(JobPostSeeder::class);
        $this->assertDatabaseCount('job_posts', 9);
    }
}
