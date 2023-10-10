<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\JobPostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HttpTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_init_url(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_home_redirect(): void
    {
        $response = $this->get('/home');

        $response->assertRedirect('/');
    }

    public function test_gigs(): void
    {
        $response = $this->get('/jobposts');

        $response->assertStatus(200);
    }

    public function test_view_jobposts_index(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobposts = JobPost::latest()->paginate(10);
        $view = $this->view('jobposts.index', ['jobposts' => $jobposts]);
        $view->assertSee('Created by seed, belongs to user1');
        $view->assertSee('Created by seed, belongs to user2');
    }

    public function test_url_jobposts_index(): void
    {
        $this->seed(JobPostSeeder::class);
        $response = $this->get('/jobposts');
        $response->assertSee('Created by seed, belongs to user1');
        $response->assertSee('Created by seed, belongs to user2');
    }

    public function test_url_jobposts_show(): void
    {
        $this->seed(JobPostSeeder::class);
        $first_jobpost = JobPost::first();
        $response = $this->get("/jobposts/{$first_jobpost->id}");
        $response->assertSee($first_jobpost->title);
    }

    public function test_delete_url_without_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $first_jobpost = JobPost::first();
        $response = $this->delete("/jobposts/{$first_jobpost->id}");
        $response->assertRedirect('/login');
    }

    public function test_delete_url_with_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobpost = JobPost::first();
        $jobpost_id = $jobpost->id;
        $this->assertDatabaseHas('job_posts', [
            'id' => $jobpost_id
        ]);
        $response = $this->actingAs($jobpost->user)->delete("/jobposts/{$jobpost_id}");
        $response->assertRedirect('/');
        $this->assertDatabaseMissing('job_posts', [
            'id' => $jobpost_id
        ]);
    }

    public function test_delete_url_with_wrong_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $user1 = User::where('name', 'user1')->first();
        $user2 = User::where('name', 'user2')->first();
        $user1_jobpost = $user1->jobposts()->first();
        $response = $this->actingAs($user2)->delete("/jobposts/{$user1_jobpost->id}");
        $response->assertForbidden();
    }

    public function test_edit_url_without_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $first_jobpost = JobPost::first();
        $response = $this->put("/jobposts/{$first_jobpost->id}");
        $response->assertRedirect('/login');
    }

    public function test_edit_url_with_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobpost = JobPost::first();
        $new_title = 'somerandomtitle3321';
        $this->assertDatabaseMissing('job_posts', [
            'title' => $new_title
        ]);
        $data = $jobpost->toArray();
        $data['title'] = $new_title;
        $response = $this->actingAs($jobpost->user)->putJson("/jobposts/{$jobpost->id}", $data);
        $response->assertRedirect('/');
        $this->assertDatabaseHas('job_posts', [
            'title' => $new_title
        ]);
    }

    public function test_edit_url_with_wrong_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $user1 = User::where('name', 'user1')->first();
        $user2 = User::where('name', 'user2')->first();
        $user1_jobpost = $user1->jobposts()->first();
        $data = $user1_jobpost->toArray();
        $new_title = 'somerandomtitle3321';
        $data['title'] = $new_title;
        $response = $this->actingAs($user2)->putJson("/jobposts/{$user1_jobpost->id}", $data);
        $response->assertForbidden();
    }

    public function test_store_url_without_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $user = User::first();
        $response = $this->postJson("/jobposts", [
            'title' => 'mytitle',
            'company' => 'mycompany',
            'location' => 'mylocation',
            'website' => 'http://google.com',
            'email' => 'myemail@gmail.com',
            'description' => 'mydescription',
            'tags' => 'react, node, django, laravel',
            'user_id' => $user->id,
        ]);
        $response->assertInternalServerError();
    }

    public function test_store_url_with_auth(): void
    {
        $this->seed(JobPostSeeder::class);
        $user = User::first();
        $response = $this->actingAs($user)->postJson("/jobposts", [
            'title' => 'mytitle',
            'company' => 'mycompany', 
            'location' => 'mylocation',
            'website' => 'http://google.com',
            'email' => 'myemail@gmail.com',
            'description' => 'mydescription',
            'tags' => 'react, node, django, laravel',
            'user_id' => $user->id,
        ]);
        $response->assertRedirect('/');
    }

    public function test_store_url_with_auth_and_logo(): void
    {
        $this->seed(JobPostSeeder::class);
        $user = User::first();
        $file = UploadedFile::fake()->image('logo1.jpg');
        $response = $this->actingAs($user)->postJson("/jobposts", [
            'title' => 'mytitle',
            'company' => 'mycompany', 
            'location' => 'mylocation',
            'website' => 'http://google.com',
            'email' => 'myemail@gmail.com',
            'description' => 'mydescription',
            'tags' => 'react, node, django, laravel',
            'user_id' => $user->id,
            'logo' => $file,
        ]);
        $response->assertRedirect('/');
    }

    public function test_edit_url_with_auth_and_logo(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobpost = JobPost::first();
        $new_title = 'somerandomtitle3321';
        $this->assertDatabaseMissing('job_posts', [
            'title' => $new_title
        ]);
        $data = $jobpost->toArray();
        $data['title'] = $new_title;
        $data['logo'] = UploadedFile::fake()->image('logo2.jpg');
        $response = $this->actingAs($jobpost->user)->putJson("/jobposts/{$jobpost->id}", $data);
        $response->assertRedirect('/');
        $this->assertDatabaseHas('job_posts', [
            'title' => $new_title
        ]);
    }

    public function test_api_users_get(): void
    {
        $this->seed(JobPostSeeder::class);
        $users = User::all()->toArray();
        $response = $this->get("/api/users");
        $response->assertOk();
        $data = $response->getOriginalContent();
        $this->assertEquals($users, $data);
    }
}
