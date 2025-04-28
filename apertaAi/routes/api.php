<?php

use App\Http\Controllers\Financeiro\PurchasesController;
use App\Http\Controllers\upload\QRCodeUploaderController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::controller(PurchasesController::class)->group(function () {
    Route::get('/purchases', 'index')->name('purchases.index');
    Route::post('/purchases', 'store')->name('purchases.store')->middleware('auth:sanctum');
    Route::get('/purchases/{purchase}', 'show')->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', 'edit')->name('purchases.edit');
    Route::put('/purchases/{purchase}', 'update')->name('purchases.update');
    Route::delete('/purchases/{purchase}', 'destroy')->name('purchases.destroy');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');//->middleware('auth:sanctum');
    Route::get('/user/{user}', 'show');
    Route::get('/user/{user}/news', 'searchUserNews');
    Route::post('/user', 'store');
    Route::put('/user/{user}', 'update');
    Route::delete('/user/{user}', 'destroy');
});

Route::controller(QRCodeUploaderController::class)->group(function () {
    Route::post('/qr-code/nf', 'read')->name('qr-code.read');
});

Route::get('/csrf-token', function (Request $request) {
    return response()->json([
        'csrf_token' => csrf_token(),
        'message' => 'CSRF token gerado com sucesso'
    ]);
});