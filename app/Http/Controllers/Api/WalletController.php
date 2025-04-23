<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $wallet = Wallet::select('balance', 'card_number', 'pin')
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Wallet retrieved successfully',
            'data' => $wallet
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'previous_pin' => 'required|digits:6',
            'new_pin' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if (!pinChecker($request->previous_pin)) {
            return response()->json([
                'status' => false,
                'message' => 'Your old pin is incorrect',
            ], 400);
        }

        $user = auth()->user();

        $wallet = Wallet::where('user_id', $user->id)
            ->update(['pin' => $request->new_pin]);

        return response()->json([
            'status' => true,
            'message' => 'Pin updated successfully',
        ]);
    }
}
