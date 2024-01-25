<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
   Route::middleware('auth:sanctum')->group(function () {
       Route::post('/token', [AuthController::class, 'isValidToken']);
       //ContiCorrente
       Route::prefix('accounts')->group(function () {
           Route::get('/', [\App\Http\Controllers\ContiCorrentiController::class, 'index']);
           Route::post('/', [\App\Http\Controllers\ContiCorrentiController::class, 'apiCreateAccount']);
           Route::post('/addJoint', [\App\Http\Controllers\ContiCorrentiController::class, 'addJoint']);
       });
       Route::prefix('transaction')->group(function () {
           Route::get('/all', [\App\Http\Controllers\TransactionController::class, 'apiGetAllTransactionsByAccount']);
           Route::get('/', [\App\Http\Controllers\TransactionController::class, 'apiGetTransactionById']);
           Route::post('/make', [\App\Http\Controllers\TransactionController::class, 'apiMakeTransaction']);
       });
   });



    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});

