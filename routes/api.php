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
// Get User Info
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**Authentication Routes */
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->get('/users', [AuthController::class, 'users']);
Route::middleware('auth:sanctum')->post('/token/validate', [AuthController::class, 'validateToken']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->post('/register', [AuthController::class, 'register']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/resetEmail', [AuthController::class, 'resetEmail']);
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);
Route::middleware(['auth:sanctum', 'ability:admin,super-admin'])->post('/update', [AuthController::class, 'update']);

// Task Routes
Route::post('/task/add', [TasksController::class, 'add']);
Route::middleware('auth:sanctum')->get('/task/{app_id}', [TasksController::class, 'get']);
Route::middleware('auth:sanctum')->get('/tasks/{filter?}', [TasksController::class, 'index']);
// Logging Error Tests
Route::middleware('auth:sanctum')->post('/log-test/{id}', [TasksController::class, 'log_test']);
// Read Application Notification
Route::middleware('auth:sanctum')->get('/notification/read/{id}', [TasksController::class, 'read_notification']);
// Error Notes
Route::middleware('auth:sanctum')->get('/notes/{app_id}/{carrier}', [TasksController::class, 'notes']);
Route::middleware('auth:sanctum')->put('/note/{id}', [TasksController::class, 'update_note']);
Route::middleware('auth:sanctum')->post('/note', [TasksController::class, 'add_note']);
Route::middleware('auth:sanctum')->delete('/note/{id}', [TasksController::class, 'drop_note']);
// Error Note Uploads
Route::middleware('auth:sanctum')->get('/note/uploads/{note_id}', [TasksController::class, 'note_uploads']);
Route::middleware('auth:sanctum')->post('/note/upload', [TasksController::class, 'note_upload']);
// Error Uploads
Route::middleware('auth:sanctum')->get('/uploads/{app_id}/{carrier}', [TasksController::class, 'uploads']);
Route::middleware('auth:sanctum')->delete('/upload/{upload_id}', [TasksController::class, 'delete_upload']);
// Status Update
Route::middleware('auth:sanctum')->post('/status/{id}', [TasksController::class, 'status']);
