<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TravelRequestController;
use App\Http\Controllers\API\V1\AuthController;

Route::get('/', function () {
    return response()->json([
        'message' => 'API is running',
    ], 200);
});

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt');
    Route::get('me', [AuthController::class, 'me'])->middleware('jwt');
});

Route::group(['prefix' => 'v1/travel-requests', 'middleware' => 'jwt'], function () {
    Route::get('/', [TravelRequestController::class, 'index']);
    Route::get('/{travelRequest}', [TravelRequestController::class, 'show']);
    Route::post('/', [TravelRequestController::class, 'store']);
    Route::patch('/{travelRequest}', [TravelRequestController::class, 'updateOwner']);
    Route::patch('/{travelRequest}/status', [TravelRequestController::class, 'update']);
});
