<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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



Route::prefix('orders')->group(function(){
    Route::post('/add', [OrderController::class, 'add']);
    Route::post('/list', [OrderController::class, 'list']);
    Route::post('/modify', [OrderController::class, 'modify']);
});
