<?php

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
