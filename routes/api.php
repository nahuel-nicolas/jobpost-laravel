<?php

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/example1', function (Request $request) {
    return response()->json([
        'posts' => [
            'title' => 'example1',
        ]
    ]);
});

Route::get('/users', [UserController::class, 'index']);

// Route::prefix('admin')->group(function () {
//     Route::get('/users', function () {
//         // Matches The "/admin/users" URL
//     });
// });

Route::prefix('jobpost')->group(function () {
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
