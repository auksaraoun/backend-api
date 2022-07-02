<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
// Route::apiResource('/category', CategoryController::class);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [AuthController::class, 'show']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::apiResource('/category', CategoryController::class);
});
