<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TasksController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**Authentication Routes */
Route::middleware('auth:sanctum')->post('/token/validate', [AuthController::class, 'validateToken']);
Route::post('/login', [AuthController::class, 'login']);
// Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->post('/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/resetEmail', [AuthController::class, 'resetEmail']);
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

// Task Routes
Route::post('/task/add', [TasksController::class, 'add']);
Route::middleware('auth:sanctum')->get('/task/{app_id}', [TasksController::class, 'get']);
Route::middleware('auth:sanctum')->get('/tasks/{filter?}', [TasksController::class, 'index']);
// Read Application Notification
Route::middleware('auth:sanctum')->get('/notification/read/{id}', [TasksController::class, 'read_notification']);
// Get Error Notes
Route::middleware('auth:sanctum')->get('/notes/{app_id}/{carrier}', [TasksController::class, 'notes']);
Route::middleware('auth:sanctum')->put('/note/{id}', [TasksController::class, 'update_note']);
Route::middleware('auth:sanctum')->post('/note', [TasksController::class, 'add_note']);
Route::middleware('auth:sanctum')->delete('/note/{id}', [TasksController::class, 'drop_note']);
// Test
Route::middleware('auth:sanctum')->post('/test/{id}', [TasksController::class, 'test']);
// Status Update
Route::middleware('auth:sanctum')->post('/status/{id}', [TasksController::class, 'status']);
