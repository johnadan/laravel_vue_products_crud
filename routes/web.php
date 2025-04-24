<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', function () {
    return view('dashboard'); // This view contains <div id="app"></div>
})->middleware('auth');

// This must be the LAST route!
Route::get('/{any}', function () {
    return view('dashboard');
})->where('any', '.*')->middleware('auth');