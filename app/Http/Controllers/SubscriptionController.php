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

            try {

                $user->newSubscription($request->id, $request->id)->create($request->method);

            } catch(\Exception $error) {

                return response([
                    'message' => 'Não foi possivel realizar a inscrição!',
                    'error'   => $error->getMessage()
                ], 400);

            }

        }

        $subscriptions = Cashier::stripe()->subscriptions->all([
            'customer' => $customer
        ])->data;

        foreach($subscriptions as &$subscription) {
            $subscription->product = Cashier::stripe()->products->retrieve($subscription->plan->product);
        }

        if($id) try {

            return response(array_filter($subscriptions, fn($s) => $s['id'] === $id)[1]);

        } catch(\Exception $error) {

            return response([
                'message' => 'Não foi encontramos sua inscrição!',
                'error'   => 'Subscription not found'
            ], 400);

        }

        return response($subscriptions);

    }

}
