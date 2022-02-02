<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;


class PriceController extends Controller {
    
    // Obtem todos os precos pre cadastrados
    public function price(Request $request, $id = null) {

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

        $starting = $request->starting_price ? [
            'starting_after' => $request->starting_price
        ] : [];

        $ending = $request->ending_price ? [
            'ending_before' => $request->ending_price
        ] : [];

        $prices = Cashier::stripe()->prices->all([
            'type'  => $request->subscriptions ? 'recurring' : 'one_time',
            'limit' => $request->limit ?: 30,
            ...$starting,
            ...$ending
        ]);

        return response([

            'prices' => $prices,

            'products' => Cashier::stripe()->products->all([
                'ids'   => array_unique(array_map(fn($price) => $price->product, $prices->data)),
                'limit' => $request->limit ?: 30
            ])

        ]);

    }

}
