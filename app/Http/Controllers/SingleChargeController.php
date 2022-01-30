<?php

namespace App\Http\Controllers;

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

}
