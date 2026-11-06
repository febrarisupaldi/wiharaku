<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::view('/peta-vihara', 'viharas.map')
    ->name('viharas.map');
