<?php

use App\Http\Controllers\Api\Vihara\PublicViharaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');
Route::view('/peta-vihara', 'viharas.map')
    ->name('viharas.map');
Route::get('/jelajahi', function () {
    return view('pages.explore');
})->name('explore');
Route::get('/peta', function () {
    return view('pages.map');
})->name('map');
Route::get('/vihara/{slug}', [PublicViharaController::class, 'show'])
    ->name('public.vihara.show');
