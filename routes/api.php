<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix(config('app.admin_prefix'))->group(function(){
    
        Route::middleware('guest')->group(function(){

            Route::controller(\App\Http\Controllers\FrontController::class)->group(function(){
                Route::post('/login-user' , 'apiUser');
            });
            
        });

        Route::middleware('auth:sanctum')->group(function(){

            Route::controller(\App\Http\Controllers\FrontController::class)->group(function(){

                Route::get('/get-machinery'                 , 'getMachinery');
                Route::get('/get-employees'                 , 'getEmployees');
                Route::post('/set-fuel-consumption'         , 'setFuelConsumption');
                Route::post('/mass-insert-fuel-consumption' , 'massInsertFuelConsumption' );
                Route::post('/mass-insert-working-hours'    , 'massInsertWorkingHours' );
                Route::post('/set-machine-working-hours'    , 'setMachineWorkingHours');
                Route::get('/latest-working-hours'          , 'latestMachineWorkingHours');
                Route::get('/dashboard'                     , 'dashboard');
                Route::get('/machine-access'                , 'machineAccess');
                
            });
            
        });

});