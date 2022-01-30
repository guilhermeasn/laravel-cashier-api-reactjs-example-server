<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller {
    
    // Obtem uma autorizacao para configurar novo metodo de pagamento
    public function intent($user_id) {
        
        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        return response([
            'STRIPE_KEY' => env('STRIPE_KEY'),
            'INTENT'     => $user->createSetupIntent()
        ]);

    }

    // Obtem, adiciona e deleta metodos de pagamento
    public function index($user_id, Request $request, $method = null) {

        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        if($request->isMethod('post')) try {

            $user->addPaymentMethod($request->ID);

        } catch(\Exception $error) {

            return response([
                'message' => 'Não foi possível adicionar o método de pagamento!',
                'error'   => $error->getMessage()
            ], 400);

        }

        if($request->isMethod('delete')) try {

            $user->deletePaymentMethods($method);

        } catch(\Exception $error) {

            return response([
                'message' => 'Não foi possível remover o método de pagamento!',
                'error'   => $error->getMessage()
            ], 400);

        }

        return response($user->paymentMethods());

    }

}
