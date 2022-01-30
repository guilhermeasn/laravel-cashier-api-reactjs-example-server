<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
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

Route::middleware('auth:sanctum')->get('/user', function(Request $request) {
    return $request->user();
});


Route::match(['POST', 'GET'], '/echo', function(Request $request) {
    return response([
        'echo' => $request->all()
    ]);
});


// Route::prefix('/cashier')->group(function() {
    
//     Route::get('/',             [ CashierController::class, 'stripe'      ]);
//     Route::post('/create',      [ CashierController::class, 'create'      ]);
//     Route::post('/balance',     [ CashierController::class, 'balance'     ]);
//     Route::post('/site',        [ CashierController::class, 'site'        ]);
//     Route::post('/intent',      [ CashierController::class, 'intent'      ]);
//     Route::post('/methods',     [ CashierController::class, 'methods'     ]);
//     Route::post('/pay_domain',  [ CashierController::class, 'pay_domain'  ]);

// });

Route::prefix('/{user_id}/customer')->group(function() {

    Route::get('/', [ CustomerController::class, 'index' ]);  # Dados do usuario/cliente
    Route::put('/', [ CustomerController::class, 'index' ]);  # Dados do usuario/cliente com alteracoes

    Route::get('/balance', [ CustomerController::class, 'balance' ]);  # Saldo e transacoes do cliente
    Route::put('/balance', [ CustomerController::class, 'balance' ]);  # Alterar saldo do cliente adicionando uma nova transacao

    Route::post('/portal', [ CustomerController::class, 'portal' ]);  # URL do site stripe para o cliente

});
