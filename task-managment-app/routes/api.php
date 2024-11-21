<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TokenVerificationMiddleware;
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
Route::post('/register', [UserController::class, 'register']);
Route::post('/login',[UserController::class,'login']);
Route::post('/sendOtpCode',[UserController::class,'sendOtpCode']);
Route::post('/verifyOtp',[UserController::class,'verifyOtp']);
Route::post('/resetPassword',[UserController::class,'resetPassword'])->middleware(TokenVerificationMiddleware::class);
Route::post('/logout',[UserController::class,'logout']);
