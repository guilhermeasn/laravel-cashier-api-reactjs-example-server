<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class SingleChargeController extends Controller {
    
    // Obtem todos os precos pre cadastrados
    public function price($id = null) {

        if($id) try {
            
            $price = Cashier::stripe()->prices->retrieve($id);
            $price->product_details = Cashier::stripe()->products->retrieve($price->product);

            return response($price);

        } catch(\Exception $error) {

            return response([
                'message' => 'Não foi possível obter os dados do produto solicitado!',
                'error'   => $error->getMessage()
            ], 400);

        }

        return response(Cashier::stripe()->prices->all([
            'type' => 'one_time'
        ])->data);

    }

    // Obtem e faz pagamentos
    public function index($user_id, Request $request, $id = null) {

        $customer = User::findCustomer($user_id, $user);
        if(!$customer) return response([
            'message' => 'Cliente não foi encontrado!',
            'error'   => 'Customer not found'
        ], 400);

        switch($request->method()) {

            case 'POST': return $this->charge_create($user, $request);
            case 'GET':  return $this->charge_read($user, $id);

        }
        
    }

    /* METODOS PRIVADOS DE SUPORTE */

    private function charge_create($user, Request $request) {

        return response([ 'charge create' ]);

    }

    private function charge_read($user, $id = null) {

        return response([ $id ? 'charge read one' : 'charge read all' ]);

    }

}
