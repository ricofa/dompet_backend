<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\TransferHistory;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->only(
            'amount',
            'pin',
            'send_to'
        );

        $validator = Validator::make($data, [
            'amount' => 'required|integer|min:10000',
            'pin' => 'required|digits:6',
            'send_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $sender = auth()->user();

        /// reciver by username or card number
        $receiver = User::select('users.id', 'users.username')
            ->join('wallets', 'wallets.user_id', 'users.id')
            ->where('users.username', $request->send_to)
            ->orWhere('wallets.card_number', $request->send_to)
            ->first();

        $pinChecker = pinChecker($request->pin);

        ///check pin
        if (!$pinChecker) {
            return response()->json([
                'message' => 'Pin not valid',
            ], 400);
        }

        /// check receiver
        if (!$receiver) {
            return response()->json([
                'message' => 'Receiver not found',
            ], 404);
        }

        /// check if sender and receiver are the same
        if ($sender->id == $receiver->id) {
            return response()->json([
                'message' => 'You cannot send money to yourself',
            ], 400);
        }

        /// check if sender has enough balance
        $senderWallet = Wallet::where('user_id', $sender->id)->first();
        if ($senderWallet->balance < $request->amount) {
            return response()->json([
                'message' => 'You do not have enough balance',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // define transaction type
            $transactionType = TransactionType::whereIn('code', ['receive', 'transfer'])
                ->orderBy('code', 'asc')
                ->get();

            $receiveTransactionType = $transactionType->first();
            $transferTransactionType = $transactionType->last();

            // define transaction code
            $transactionCode = strtoupper(Str::random(10));

            //define payment method
            $paymentMethod = PaymentMethod::where('code', 'bca_va')->first();

            // create transaction for transfer
            $transferTransaction = Transaction::create([
                'user_id' => $sender->id,
                'transaction_type_id' => $transferTransactionType->id,
                'description' => 'Transfer funds to ' . $receiver->username,
                'amount' => -$request->amount,
                'transaction_code' => $transactionCode,
                'status' => 'success',
                'payment_method_id' => $paymentMethod->id
            ]);

            // update sender wallet balance
            $senderWallet->decrement('balance', $request->amount);

            // create transaction for receive
            $receiveTransaction = Transaction::create([
                'user_id' => $receiver->id,
                'transaction_type_id' => $receiveTransactionType->id,
                'description' => 'Receive funds from ' . $sender->username,
                'amount' => $request->amount,
                'transaction_code' => $transactionCode,
                'status' => 'success',
                'payment_method_id' => $paymentMethod->id
            ]);

            // update receiver wallet balance
            Wallet::where('user_id', $receiver->id)->increment('balance', $request->amount);

            //create history transaction
            TransferHistory::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'transaction_code' => $transactionCode,
            ]);

            DB::commit();
            return response(['message' => 'Transfer success'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
