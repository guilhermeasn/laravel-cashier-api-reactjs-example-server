<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\SingleChargeController;
use App\Http\Controllers\SubscriptionController;
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


Route::match(['POST', 'GET'], '/echo', fn(Request $request) => response($request->all()));  # rota de teste; retona o que foi enviado


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

    Route::get   ('/', [ PaymentMethodController::class, 'index' ]);  # Obtem todos os metodos de pagamento do cliente
    Route::post  ('/', [ PaymentMethodController::class, 'index' ]);  # Gera um novo metodo de pagamento para o cliente intencionado
    Route::put   ('/', [ PaymentMethodController::class, 'index' ]);  # Torna um metodo de pagamento como o padrao
    Route::delete('/', [ PaymentMethodController::class, 'index' ]);  # Deleta um metodo de pagamento do cliente

});

Route::prefix('/price')->group(function() {

    Route::get('/',      [ PriceController::class, 'price' ]);  # Obtem todos os produtos ou assinaturas cadastrados
    Route::get('/{id}',  [ PriceController::class, 'price' ]);  # Obtem um produto ou assinatura cadastrado

});

Route::prefix('/{user_id}/singleCharge')->group(function() {

    Route::post('/',     [ SingleChargeController::class, 'index' ]);  # Faz um pagamento de acordo com um metodo de pagamento salvo
    Route::get ('/',     [ SingleChargeController::class, 'index' ]);  # Obtem todos os pagamentos realizados
    Route::get ('/{id}', [ SingleChargeController::class, 'index' ]);  # Obtem um pagamento especifico

});

Route::prefix('/{user_id}/subscription')->group(function() {

    Route::get   ('/{id}', [ SubscriptionController::class, 'index' ]);  # obtem uma assinatura
    Route::get   ('/',     [ SubscriptionController::class, 'index' ]);  # obtem as assinaturas do cliente
    Route::post  ('/',     [ SubscriptionController::class, 'index' ]);  # registra uma nova assinatura
    Route::delete('/',     [ SubscriptionController::class, 'index' ]);  # cancela uma assinatura

});
