<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Cashier\Cashier;
use Illuminate\Http\Request;

class CashierController extends Controller {
    
    // Envia a chave publica do stipe
    public function stripe() {

        return response([ 'STRIPE_KEY' => env('STRIPE_KEY') ]);

    }

    // Cria ou recupera um cliente stripe
    private static function getStripeCustomer($user_id, &$user = null) {
        
        $user = User::find($user_id);
        if(!$user) return response([ 'message' => 'User not found!'], 400);

        return $user->createOrGetStripeCustomer();

    }

    // Cria, recupera ou altera um cliente stripe
    public function create(Request $request) {

        $stripeCustomer = self::getStripeCustomer($request->user, $user);
        if(!$user) return $stripeCustomer; // stripe customer fail message

        if($request->phone) $stripeCustomer = $user->updateStripeCustomer([
            'phone' => $request->phone
        ]);

        return response([
            'userBillable'   => Cashier::findBillable($user->stripe_id),
            'stripeCustomer' => $stripeCustomer,
        ]);

    }

    // Altera e recupera a conta corrente do cliente stripe
    public function balance(Request $request) {

        $stripeCustomer = self::getStripeCustomer($request->user, $user);
        if(!$user) return $stripeCustomer;; // stripe customer fail message

        if($request->apply) $user->applyBalance($request->apply, $request->comment ?? '');

        $transactions = [];

        foreach ($user->balanceTransactions() as $transaction) {
            array_unshift($transactions, $transaction->amount() . ' ' . ($transaction->description ?: 'without description'));
        }

        return response([
            'transactions' => $transactions,
            'balance'      => $user->balance()
        ]);

    }

    // Site da Stripe para o usuario gerenciar seus pagamentos e informacoes
    public function site(Request $request) {

        $stripeCustomer = self::getStripeCustomer($request->user, $user);
        if(!$user) return $stripeCustomer;; // stripe customer fail message
        
        return response([
            'url' => $user->billingPortalUrl($request->back ?: env('APP_URL'))
        ]);

    }

    // Registra uma intencao de pagamento para determinado usuario
    public function intent(Request $request) {

        $stripeCustomer = self::getStripeCustomer($request->user, $user);
        if(!$user) return $stripeCustomer;; // stripe customer fail message

        return response([
            'STRIPE_KEY' => env('STRIPE_KEY'),
            'INTENT'     => $user->createSetupIntent()
        ]);
        
    }

}
