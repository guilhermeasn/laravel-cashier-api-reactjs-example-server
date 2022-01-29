<?php

use App\Http\Controllers\CashierController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::match(['POST', 'GET'], '/echo', function (Request $request) {
    return response([
        'echo' => $request->all()
    ]);
});


Route::prefix('/cashier')->group(function () {
    
    Route::get('/',         [ CashierController::class, 'test'    ]);
    Route::post('/create',  [ CashierController::class, 'create'  ]);
    Route::post('/balance', [ CashierController::class, 'balance' ]);
    Route::post('/site',    [ CashierController::class, 'site'    ]);

});
