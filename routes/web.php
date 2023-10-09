<?php

use App\Http\Controllers\JobPostController;
use App\Http\Controllers\UserController;
use App\Models\JobPost;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// All Jobposts
Route::get('/jobposts', [JobPostController::class, 'index']);

// Show Create Form
Route::get('/jobposts/create', [JobPostController::class, 'create']);
// Route::get('/jobposts/create', [JobpostController::class, 'create'])->middleware('auth');

// Store Jobpost Data
Route::post('/jobposts', [JobPostController::class, 'store']);

// Show Edit Form
Route::get('/jobposts/{jobpost}/edit', [JobpostController::class, 'edit'])->middleware('auth');

// Update Jobpost
Route::put('/jobposts/{jobpost}', [JobpostController::class, 'update'])->middleware('auth');

// Delete Jobpost
Route::delete('/jobposts/{jobpost}', [JobpostController::class, 'destroy'])->middleware('auth');

// Manage Jobposts
Route::get('/jobposts/manage', [JobpostController::class, 'manage'])->middleware('auth');

// Single Jobpost
Route::get('/jobposts/{jobpost}', [JobpostController::class, 'show']);

// Show Register/Create Form
Route::get('/register', [UserController::class, 'create'])->middleware('guest')->name('register');;

// Create New User
Route::post('/users', [UserController::class, 'store']);

// Log User Out
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

// Show Login Form
Route::get('/login', [UserController::class, 'login'])->name('login')->middleware('guest');

// Log In User
Route::post('/users/authenticate', [UserController::class, 'authenticate']);

Route::permanentRedirect('/home', '/');