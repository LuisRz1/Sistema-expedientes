<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ImportController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/healthz', HealthController::class)
    ->name('healthz')
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        PreventRequestForgery::class,
    ]);

Route::resource('expedientes', ExpedienteController::class);

Route::prefix('importar')->name('import.')->group(function () {
    Route::get('/',              [ImportController::class, 'index'])->name('index');
    Route::post('/subir',        [ImportController::class, 'upload'])->name('upload');
    Route::post('/procesar',     [ImportController::class, 'process'])->name('process');
    Route::post('/corregir',     [ImportController::class, 'fixUnmatched'])->name('fix');
    Route::post('/crear-catalogo', [ImportController::class, 'createCatalogItem'])->name('catalog.create');
});
