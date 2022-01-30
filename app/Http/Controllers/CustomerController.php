<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Cashier\Cashier;

class CustomerController extends Controller {

    // Registra novo usuario
    public function customer(Request $request) {

        $validator = User::validator($request->all());

        if ($validator->fails()) return response([
            'message' => 'Não foi possível registrar o usuário!',
            'error'   => 'Validation new user fail',
            'errors'  => $validator->errors()
        ], 400);

        $user = new User();

        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        return response($user->save() ? $user : [
            'message' => 'Desculpe-nos! Não foi possível registrar o usuário!',
            'error'   => 'Failed to save new user',
        ], ($user->id) ? 200 : 500);

    }
    
    // Obtem e altera dados do cliente stripe
    public function index($user_id, Request $request) {

        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        if($request->isMethod('put')) {

            try {

                $customer = $user->updateStripeCustomer($request->all());

            } catch(\Exception $error) {

                return response([
                    'message' => 'Não foi possível alterar as informações do cliente!',
                    'error'   => $error->getMessage()
                ], 400);

            }

        }

        return response([
            'user'     => Cashier::findBillable($customer->id),  # dados interno do usuario, igual a $user
            'customer' => $customer
        ]);

    }

    // Obtem e altera o saldo do cliente stripe
    public function balance($user_id, Request $request) {

        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        if($request->isMethod('put') and $request->amount) {
            $user->applyBalance($request->amount, $request->description);
        }

        $transactions = [];

        foreach ($user->balanceTransactions() as $transaction) {
            array_unshift($transactions, [
                'amout'       => $transaction->amount(),
                'description' => $transaction->description
            ]);
        }

        return response([
            'transactions' => $transactions,
            'balance'      => $user->balance()
        ]);

    }

    // Cria URL para a Stripe para o usuario gerenciar seus pagamentos e informacoes com retorno
    public function portal($user_id, Request $request) {

        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        if(!$request->return_url) return response([
            'message' => 'URL de retorno não informado',
            'error'   => 'Required parameter: return_url'
        ], 400);

        try {

            return response([ 'url' => $user->billingPortalUrl($request->return_url) ]);

        } catch(\Exception $error) {

            return response([
                'message' => 'Não foi possível gerar a url para o site da stripe!',
                'error'   => $error->getMessage()
            ], 400);

        }        

    }

}
