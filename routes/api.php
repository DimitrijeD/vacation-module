<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VacationRequestController;
use App\Http\Controllers\AuthController;

Route::get('user', function (){
    return auth()->user();
})->middleware('auth:sanctum');

Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'vacations'], function (){
    Route::get('all', [VacationRequestController::class, 'getAllVacationRequests']);
    Route::get('get-pending', [VacationRequestController::class, 'getMyPendingVacationRequest']);
    Route::post('store', [VacationRequestController::class, 'store']);
    Route::post('update', [VacationRequestController::class, 'update']);
    Route::delete('delete-pending', [VacationRequestController::class, 'deleteMyPendingVacationRequest']);

    Route::group(['middleware' => ['manager_role']], function (){
        Route::post('approve', [VacationRequestController::class, 'approveVacationRequest']);
        Route::post('reject', [VacationRequestController::class, 'rejectVacationRequest']);
    });
});
