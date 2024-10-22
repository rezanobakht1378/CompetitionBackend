<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompetitionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('logout',[AuthController::class,'logout']);
    Route::post('/competition/create',[CompetitionController::class,'store']);
    // Route::resource('products', ProductController::class);
});
