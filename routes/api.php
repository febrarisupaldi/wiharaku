<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\ProvinceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\SubdistrictController;
use App\Http\Controllers\Api\ViharaController;

Route::prefix('v1')->group(function () {

    // MASTER WILAYAH
    Route::prefix('regions')->group(function () {
        Route::get('/provinces', [
            ProvinceController::class,
            'index'
        ]);

        Route::get('/cities', [
            CityController::class,
            'getCities'
        ]);

        Route::get('/districts', [
            DistrictController::class,
            'getDistricts'
        ]);

        Route::get('/subdistricts', [
            SubdistrictController::class,
            'getSubdistricts'
        ]);
    });

    // VIHARA
    Route::prefix('viharas')->group(function () {
        Route::get('/', [
            ViharaController::class,
            'index'
        ]);

        Route::get('/{id}', [
            ViharaController::class,
            'show'
        ])->whereNumber('id');

        // Sementara untuk development.
        // Nanti wajib dilindungi middleware admin.
        Route::post('/', [
            ViharaController::class,
            'store'
        ]);

        Route::put('/{id}', [
            ViharaController::class,
            'update'
        ])->whereNumber('id');

        Route::delete('/{id}', [
            ViharaController::class,
            'destroy'
        ])->whereNumber('id');

        Route::post('/{id}/publish', [
            ViharaController::class,
            'publish'
        ])->whereNumber('id');

        Route::post('/{id}/unpublish', [
            ViharaController::class,
            'unpublish'
        ])->whereNumber('id');
    });
});
