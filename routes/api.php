<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SingleChargeController;
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


Route::post('/customer', [ CustomerController::class, 'customer' ]);  # Novo usuario/cliente

Route::prefix('/{user_id}/customer')->group(function() {

    Route::get('/', [ CustomerController::class, 'index' ]);  # Dados do usuario/cliente
    Route::put('/', [ CustomerController::class, 'index' ]);  # Dados do usuario/cliente com alteracoes

    Route::get('/balance', [ CustomerController::class, 'balance' ]);  # Saldo e transacoes do cliente
    Route::put('/balance', [ CustomerController::class, 'balance' ]);  # Alterar saldo do cliente adicionando uma nova transacao

    Route::post('/portal', [ CustomerController::class, 'portal' ]);  # Gera URL do site stripe para o cliente

});

Route::prefix('/{user_id}/paymentMethod')->group(function() {

    Route::get('/intent', [ PaymentMethodController::class, 'intent' ]);  # Dados para a intencao de gerar novo metodo de pagamento

    Route::get   ('/',         [ PaymentMethodController::class, 'index' ]);  # Obtem todos os metodos de pagamento do cliente
    Route::get   ('/{method}', [ PaymentMethodController::class, 'index' ]);  # Obtem um metodo de pagamento do cliente
    Route::post  ('/',         [ PaymentMethodController::class, 'index' ]);  # Gera um novo metodo de pagamento para o cliente intencionado
    Route::delete('/{method}', [ PaymentMethodController::class, 'index' ]);  # Deleta um metodo de pagamento do cliente

});

Route::prefix('/singleCharge')->group(function() {

    Route::get('/price',      [ SingleChargeController::class, 'price' ]);  # Obtem todos os produtos de pagamento unico
    Route::get('/price/{id}', [ SingleChargeController::class, 'price' ]);  # Obtem um produto de pagamento unico especifivo

});

Route::prefix('/{user_id}/singleCharge')->group(function() {

    Route::post('/',     [ SingleChargeController::class, 'index' ]);  # Faz um pagamento de acordo com um metodo de pagamento salvo
    Route::get ('/',     [ SingleChargeController::class, 'index' ]);  # Obtem todos os pagamentos realizados
    Route::get ('/{id}', [ SingleChargeController::class, 'index' ]);  # Obtem um pagamento especifico

});

Route::prefix('/{user_id}/subscription')->group(function() {

});
