<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CashierController extends Controller {
    
    public function test() {

        return response([ 'CashierController communication test ok!' ]);

    }

}
