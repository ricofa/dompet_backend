<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') ? $request->query('limit') : 10;
        $user = auth()->user();

        $relations = [
            'paymentMethod:id,name,code,thumbnail',
            'transactionType:id,name,code,action,thumbnail',
        ];

        $transactions = Transaction::with($relations)
            ->where('user_id', $user->id)
            ->where('status', 'success')
            ->orderBy('id', 'desc')
            ->paginate($limit);

        $transactions->getCollection()->transform(function ($item) {
            $paymentMethodThumbnail = $item->paymentMethod->thumbnail ?
                url('banks/' . $item->paymentMethod->thumbnail) : '';
            $item->paymentMethod = clone $item->paymentMethod;
            $item->paymentMethod->thumbnail = $paymentMethodThumbnail;
            // $paymentMethod = $item->paymentMethod;
            // $item->paymentMethod->thumbnail = $paymentMethod->thumbnail ? url('banks/' . $paymentMethod->thumbnail) : "";

            $transactionType = $item->transactionType;
            $item->transactionType->thumbnail = $transactionType->thumbnail ?
                url('transaction-type/' . $transactionType->thumbnail) : "";

            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions
        ]);
    }
}
