<?php

use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\PatientsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('patients')
    ->group(function () {
        Route::post('{id}/request', [PatientsController::class, 'createQuotes']);
    });

Route::prefix('doctors')
    ->group(function () {
        Route::put('quote/{id}', [DoctorsController::class, 'changeStatus']);
        Route::get('all-date/{id}', [DoctorsController::class, 'getAllByDate']);
    });
