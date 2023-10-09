<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPost;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Validation\Rule;
use Storage;

class JobPostController extends Controller
{
    // Show all jobposts
    public function index() {
        return view('jobposts.index', [
            // 'jobposts' => JobPost::latest()->filter(request(['tag', 'search']))->paginate(6)
            'jobposts' => JobPost::latest()->paginate(5)
        ]);
    }

    // Show Create Form
    public function create() {
        // dd(['a' => 1]);
        return view('jobposts.create');
    }

    // Store Jobpost Data
    public function store(Request $request) {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('job_posts', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();
        // $formFields['user_id'] = User::first()->id;

        JobPost::create($formFields);

        return redirect('/')->with('message', 'Jobpost created successfully!');
    }

    // Delete Jobpost
    public function destroy(Jobpost $jobpost) {
        // Make sure logged in user is owner
        if($jobpost->user_id != auth()->id()) {
            abort(403, 'Unauthorized Action');
        }
        
        if($jobpost->logo && Storage::disk('public')->exists($jobpost->logo)) {
            Storage::disk('public')->delete($jobpost->logo);
        }
        $jobpost->delete();
        return redirect('/')->with('message', 'Jobpost deleted successfully');
    }

    //Show single jobpost
    public function show(Jobpost $jobpost) {
        return view('jobposts.show', [
            'jobpost' => $jobpost
        ]);
    }

    // Show Edit Form
    public function edit(Jobpost $jobpost) {
        return view('jobposts.edit', ['jobpost' => $jobpost]);
    }

    // Update Jobpost Data
    public function update(Request $request, Jobpost $jobpost) {
        // Make sure logged in user is owner
        if($jobpost->user_id != auth()->id()) {
            // dd([auth()->id(), $jobpost->user_id]);
            abort(403, 'Unauthorized Action');
        }
        
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $jobpost->update($formFields);

        return back()->with('message', 'Jobpost updated successfully!');
    }

    // Manage Jobpost
    public function manage() {
        return view('jobposts.manage', ['jobposts' => auth()->user()->jobposts()->get()]);
    }
}
