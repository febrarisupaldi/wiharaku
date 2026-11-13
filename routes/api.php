<?php

use App\Http\Controllers\Api\Region\CityController;
use App\Http\Controllers\Api\Region\DistrictController;
use App\Http\Controllers\Api\Region\ProvinceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Region\SubdistrictController;
use App\Http\Controllers\Api\Vihara\ViharaController;
use App\Http\Controllers\Api\Vihara\ViharaPhotoController;
use App\Http\Controllers\Api\Vihara\ViharaSearchController;

Route::prefix('v1')->group(function () {

    // MASTER WILAYAH
    Route::prefix('regions')->group(function () {
        Route::get('/provinces', [
            ProvinceController::class,
            'index'
        ]);

        Route::get('/cities', [
            CityController::class,
            'index'
        ]);

        Route::get('/districts', [
            DistrictController::class,
            'index'
        ]);

        Route::get('/subdistricts', [
            SubdistrictController::class,
            'index'
        ]);
    });


    // VIHARA
    Route::prefix('viharas')->group(function () {
        Route::get('/', [
            ViharaController::class,
            'index'
        ]);

        Route::get('/nearby', [ViharaSearchController::class, 'nearby']);
        Route::get('/radius', [ViharaSearchController::class, 'radius']);
        Route::get('/map', [ViharaSearchController::class, 'map']);

        Route::get('/{id}', [
            ViharaController::class,
            'show'
        ]);

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

        Route::post('/{id}/submit', [
            ViharaController::class,
            'submit'
        ])->whereNumber('id');

        Route::post('/{id}/verify', [
            ViharaController::class,
            'verify'
        ])->whereNumber('id');

        Route::post('/{id}/reject', [
            ViharaController::class,
            'reject'
        ])->whereNumber('id');

        Route::post('/{id}/publish', [
            ViharaController::class,
            'publish'
        ])->whereNumber('id');

        Route::post('/{id}/unpublish', [
            ViharaController::class,
            'unpublish'
        ])->whereNumber('id');

        Route::get(
            '/{id}/photos',
            [ViharaPhotoController::class, 'index']
        );

        Route::post(
            '/{id}/photos',
            [ViharaPhotoController::class, 'store']
        );

        Route::patch(
            '{id}/photos/{photoId}/review',
            [ViharaPhotoController::class, 'review']
        );

        Route::patch(
            '{id}/photos/{photoId}/primary',
            [ViharaPhotoController::class, 'setPrimary']
        );

        Route::delete(
            '{id}/photos/{photoId}',
            [ViharaPhotoController::class, 'destroy']
        );
    });
});
