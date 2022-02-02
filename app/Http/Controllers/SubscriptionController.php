<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class SubscriptionController extends Controller {
    
    // Obtem e registra assinaturas
    public function index($user_id, Request $request, $id = null) {

        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        if($request->isMethod('post')) {

            $sr = $user->newSubscription(
                $request->id, $request->id
            )->create($request->method);

            return response($sr);

        }

        if($id) return response(Cashier::stripe()->subscriptions->retrieve($id));

        return response(Cashier::stripe()->subscriptions->all([
            'customer' => $customer
        ]));

    }

}
