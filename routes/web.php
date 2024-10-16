<?php

use App\Http\Controllers\IDMeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login/idme', [IDMeController::class, 'redirectToIDMe'])->name('login.idme');
Route::get('/callback', [IDMeController::class, 'handleCallback']);
