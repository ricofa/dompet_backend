<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Validator;

// belum selesai
class TopUpController extends Controller
{
    public function store(Request $request){
        $data = $request->only('amount', 'pin', 'payment_method_code');

        $validator = Validator::make($data, [
            'amount' => 'required|integer|min:10000', 
            'amount' => 'required|digits:6',
            'payment_method_code' => '',
        ]);
    }    
}
