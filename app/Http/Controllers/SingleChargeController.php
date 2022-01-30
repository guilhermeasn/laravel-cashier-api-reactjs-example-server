<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

use function PHPSTORM_META\map;

class SingleChargeController extends Controller {
    
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
            'type'  => 'one_time',
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

        if(!$request->price) return response([
            'message' => 'Nenhum produto ou preço informado!',
            'error'   => 'Required parameter: price'
        ], 400);

        try {

            if(is_numeric($request->price)) return response($user->charge($request->price, $request->method));
            else return response($user->invoicePrice($request->price, $request->quantity ?: 1, [ 'default_payment_method' => $request->method ]));

        } catch(\Exception $error) {

            return response([
                'message' => 'Não foi possivel realizar o pagamento!',
                'error'   => $error->getMessage()
            ], 400);

        }

        return response([ 'charge create' ]);

    }

    private function charge_read($user, $id = null) {

        if($id) {

            if(preg_match('/^(\w+)_(.+)$/', $id, $match)) try {
                
                switch($match[1]) {

                    case 'in': return response($user->findInvoice($id));
                    case 'ch': return response(Cashier::stripe()->charges->retrieve($id));

                }

            } catch(\Exception $error) {

                return response([
                    'message' => 'Pagamento não foi encontrado!',
                    'error'   => $error->getMessage()
                ], 400);

            }

            return response([
                'message' => 'Identificação do pagamento desconhecido!',
                'error'   => 'Pattern of ID charge or invoice fail'
            ], 400);

        }

        return response([
            'invoices' => $user->invoices(),
            'charges'  => Cashier::stripe()->charges->all([
                'customer' => $user->stripe_id
            ])->data
        ]);

    }

}
