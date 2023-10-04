<?php

namespace Tests\Unit;

use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class JobPostModelTest extends TestCase
{
    // use RefreshDatabase;
    use RefreshDatabase, DatabaseMigrations;

    // public function setUp(): void
    // {
    //     parent::setUp();
    //     // $this->user = \App\User::first();
    // }

    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_job_post_creation(): void
    {
        $jobpost = JobPost::create([
            'title' => 'mytitle',
            'company' => 'mycompany',
            'location' => 'mylocation',
            'website' => 'http://google.com',
            'email' => 'myemail@gmail.com',
            'description' => 'mydescription',
            'tags' => 'react, node, django, laravel'
        ]);

        $this->assertModelExists($jobpost);
        $this->assertDatabaseHas('job_posts', [
            'title' => 'mytitle'
        ]);
    }
}
