<?php

namespace Tests\Feature;

use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\JobPostSeeder;

class APITest extends TestCase
{
    use RefreshDatabase;

    public function test_api_users_get(): void
    {
        $this->seed(JobPostSeeder::class);
        $users = User::all()->toArray();
        $response = $this->get("/api/users");
        $response->assertOk();
        $data = $response->getOriginalContent();
        $this->assertEquals($users, $data);
    }

    public function test_api_private_users_get(): void
    {
        $this->seed(JobPostSeeder::class);
        $users = User::all()->toArray();
        $userData = [
            'name' => 'user1',
            'email' => 'user1@email.com',
            'password' => 'user1password'
        ];
        $user = User::where('email', $userData['email'])->first();
        $response = $this->postJson("/api/login", [
            'email' => $userData['email'],
            'password' => $userData['password']
        ]);
        $response->assertCreated();
        $responseData = $response->getOriginalContent();
        $token = $responseData['token'];

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}"
        ])->get("/api/private/users");
        $response->assertOk();
        $data = $response->getOriginalContent();
        $this->assertEquals($users, $data);
    }

    public function test_jobpost_get(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobposts = JobPost::all()->take(7)->toArray();
        $response = $this->get("/api/jobposts");
        $response->assertOk();
        $data = $response->getOriginalContent();
        $this->assertEquals($jobposts, $data);
    }

    public function test_jobpost_optional_param_get(): void
    {
        $this->seed(JobPostSeeder::class);

        $response = $this->get("/api/jobposts");
        $response->assertOk();
        $data = $response->getOriginalContent();
        $this->assertEquals(count($data), 7);

        $response = $this->get("/api/jobposts/3");
        $response->assertOk();
        $data = $response->getOriginalContent();
        $this->assertEquals(count($data), 3);
    }

    public function test_api_store_jobpost(): void
    {
        $this->seed(JobPostSeeder::class);
        $userData = [
            'name' => 'user1',
            'email' => 'user1@email.com',
            'password' => 'user1password'
        ];
        $user = User::where('email', $userData['email'])->first();
        $response = $this->postJson("/api/login", [
            'email' => $userData['email'],
            'password' => $userData['password']
        ]);
        $response->assertCreated();
        $responseData = $response->getOriginalContent();
        $token = $responseData['token'];
        $auth_header = ['Authorization' => "Bearer {$token}"];

        $new_jobpost = [
            'title' => 'new api jobpost',
            'company' => 'new company',
            'location' => 'new york',
            'website' => 'https://google.com',
            'email' => 'jobpost@mail.com',
            'description' => 'blah blah',
            'tags' => 'lorem, ipsum, latin',
            'user_id' => $user->id
        ];

        $this->assertDatabaseMissing('job_posts', [
            'title' => $new_jobpost['title']
        ]);
        $response = $this->withHeaders($auth_header)->postJson("/api/jobposts", $new_jobpost);
        $response->assertRedirect('/');
        $this->assertDatabaseHas('job_posts', [
            'title' => $new_jobpost['title']
        ]);
    }

    public function test_api_update_jobpost(): void
    {
        $this->seed(JobPostSeeder::class);
        $userData = [
            'name' => 'user1',
            'email' => 'user1@email.com',
            'password' => 'user1password'
        ];
        $user = User::where('email', $userData['email'])->first();
        $response = $this->postJson("/api/login", [
            'email' => $userData['email'],
            'password' => $userData['password']
        ]);
        $response->assertCreated();
        $responseData = $response->getOriginalContent();
        $token = $responseData['token'];
        $auth_header = ['Authorization' => "Bearer {$token}"];

        $jobpost = $user->jobposts()->first();
        $new_jobpost = $jobpost->toArray();
        $new_jobpost['title'] = 'new title';

        $this->assertDatabaseMissing('job_posts', [
            'title' => $new_jobpost['title']
        ]);
        $this->assertDatabaseHas('job_posts', [
            'title' => $jobpost->title
        ]);
        $response = $this->withHeaders($auth_header)->putJson("/api/jobposts/{$jobpost->id}", $new_jobpost);
        $response->assertRedirect('/');
        $this->assertDatabaseHas('job_posts', [
            'title' => $new_jobpost['title']
        ]);
        $this->assertDatabaseMissing('job_posts', [
            'title' => $jobpost->title
        ]);
    }

    public function test_api_destroy_jobpost(): void
    {
        $this->seed(JobPostSeeder::class);
        $userData = [
            'name' => 'user1',
            'email' => 'user1@email.com',
            'password' => 'user1password'
        ];
        $user = User::where('email', $userData['email'])->first();
        $response = $this->postJson("/api/login", [
            'email' => $userData['email'],
            'password' => $userData['password']
        ]);
        $response->assertCreated();
        $responseData = $response->getOriginalContent();
        $token = $responseData['token'];
        $auth_header = ['Authorization' => "Bearer {$token}"];

        $jobpost = $user->jobposts()->first();

        $this->assertDatabaseHas('job_posts', [
            'title' => $jobpost->title
        ]);
        $response = $this->withHeaders($auth_header)->delete("/api/jobposts/{$jobpost->id}");
        $response->assertRedirect('/');
        $this->assertDatabaseMissing('job_posts', [
            'title' => $jobpost->title
        ]);
    }
}
