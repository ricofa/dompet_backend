<?php

use App\Models\User;
use App\Models\Wallet;

function getUser($param)
{
    // $user = User::where('id', $param)
    //             ->orWhere('email', $param)
    //             ->orWhere('username', $param)
    //             ->first();

    // $wallet = Wallet::where('user_id', $user->id)->first();
    // $user->profile_picture = $user->profile_picture ? url('storage/'.$user->profile_picture) : "";
    // $user->ktp = $user->ktp ? url('storage/'.$user->ktp) : "";
    // $user->balance = $wallet->balance;
    // $user->card_number = $wallet->card_number;
    // $user->pin = $wallet->pin;

    // return $user;

    $user = User::where('id', $param)
        ->orWhere('email', $param)
        ->orWhere('username', $param)
        ->first();

    if (!$user) {
        return null; // or handle the error as needed
    }

    $wallet = Wallet::where('user_id', $user->id)->first();

    $user->profile_picture = $user->profile_picture ? url('storage/' . $user->profile_picture) : "";
    $user->ktp = $user->ktp ? url('storage/' . $user->ktp) : "";

    if (!$wallet) {
        $user->balance = 0; // or some default value
        $user->card_number = null; // or some default value
        $user->pin = null; // or some default value
    } else {
        $user->balance = $wallet->balance;
        $user->card_number = $wallet->card_number;
        $user->pin = $wallet->pin;
    }

    return $user;
}
