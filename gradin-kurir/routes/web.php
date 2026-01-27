<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/kurir', [App\Http\Controllers\KurirController::class, 'index']);
Route::get('/kurir/all', [App\Http\Controllers\KurirController::class, 'show']);
Route::post('/kurir', [App\Http\Controllers\KurirController::class, 'store']);
Route::post('/kurir/{id}', [App\Http\Controllers\KurirController::class, 'update']);
Route::delete('/kurir/{id}', [App\Http\Controllers\KurirController::class, 'destroy']);