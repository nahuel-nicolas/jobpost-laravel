<?php

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('sanctumlogin')->middleware('guest');

Route::get('/users', [UserController::class, 'index']);

Route::prefix('jobposts')->group(function () {
    Route::get('/', function (Request $request) {
        $posts = JobPost::latest()->take(7)->get()->toArray();
        return response()->json($posts);
    });

    Route::get('/{max?}/{title?}', function (Request $request, ?string $max = null, ?string $title = null) {
        $posts = $title !== null ? JobPost::where('title', $title) : JobPost::latest();
        $max = $max === null ? 7 : intval($max);
        $posts = $posts->take($max)->get()->toArray();
        return response()->json($posts);
    });
});

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/private/users', [UserController::class, 'index']);

    Route::prefix('jobposts')->group(function () {
        Route::post('/', [JobPostController::class, 'store']);
        Route::put('/{jobpost}', [JobPostController::class, 'update']);
        Route::delete('/{jobpost}', [JobPostController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
