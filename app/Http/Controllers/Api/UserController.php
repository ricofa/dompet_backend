<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show()
    {
        $user = getUser(auth()->user()->id);

        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    public function getUserByUsername(Request $request, $username)
    {
        $user = User::select('id', 'name', 'username', 'verified', 'profile_picture')
            ->where('username', 'LIKE', '%' . $username . '%')
            ->where('id', '<>', auth()->user()->id)
            ->get();

        $user->map(function ($item) {
            $item->profile_picture = $item->profile_picture ? url('storage/' . $item->profile_picture) : "";
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    public function update(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);
            $data = $request->only('name', 'username', 'ktp', 'email', 'password');

            if ($request->username != $user->username) {
                $isExistUsername = User::where('username', $request->username)->exists();
                if ($isExistUsername) {
                    return response([
                        'message' => 'Username already exists',
                    ], 409);
                }
            }

            if ($request->email != $user->email) {
                $isExistEmail = User::where('email', $request->email)->exists();
                if ($isExistEmail) {
                    return response([
                        'message' => 'Email already exists',
                    ], 409);
                }
            }

            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }

            if ($request->profile_picture) {
                $profilePicture = uploadBase64Image($request->profile_picture);
                $data['profile_picture'] = $profilePicture;

                if ($user->profile_picture) {
                    Storage::delete('public/' . $user->profile_picture);
                }
            }

            if ($request->ktp) {
                $ktp = uploadBase64Image($request->ktp);
                $data['ktp'] = $ktp;
                $data['verified'] = true;

                if ($user->ktp) {
                    Storage::delete('public/' . $user->ktp);
                }
            }

            $user->update($data);

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                // 'data' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function isEmailExist(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $isEmailExist = User::where('email', request()->email)->exists();

        return response()->json([
            'data' => [
                'is_email_exist' => $isEmailExist
            ]
        ]);
    }
}
