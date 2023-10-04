<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobPostController extends Controller
{
    // Show Create Form
    public function create() {
        return view('listings.create');
    }
}
