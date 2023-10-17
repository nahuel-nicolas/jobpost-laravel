<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Seeders\JobPostSeeder;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Session;

class ViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_flash_message(): void
    {
        Session::flash('message', 'This is a test!'); 
        $view = $this->view('components.flash-message');
        $view->assertSeeText('This is a test!');
    }

    public function test_view_jobposts_card(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobpost = JobPost::first();
        $view = $this->view('components.jobpost-card', ['jobpost' => $jobpost]);
        $view->assertSeeText($jobpost->title);
        $view->assertSeeText($jobpost->company);
        $view->assertSeeText($jobpost->location);
    }

    public function test_jobposts_tags(): void
    {
        $view = $this->view('components.jobpost-tags', ['tagsCsv' => 'this is,a test']);
        $view->assertSeeText('this is');
        $view->assertSeeText('a test');
    }
    
    public function test_view_jobposts_index(): void
    {
        $this->seed(JobPostSeeder::class);
        $jobposts = JobPost::latest()->paginate(10);
        $view = $this->view('jobposts.index', ['jobposts' => $jobposts]);
        $view->assertSeeText('Created by seed, belongs to user1');
        $view->assertSeeText('Created by seed, belongs to user2');
    }
}
