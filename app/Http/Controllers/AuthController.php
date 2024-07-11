<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
            'referer_id' => 'required|exists:users,sponsor_id',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sponsor_id = bin2hex(random_bytes(4)); // Generate a random alphanumeric string of length 8

        $user = User::create([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'password' => Hash::make($request->password),
            'sponsor_id' => $sponsor_id,
            'referer_id' => $request->referer_id,
            'dob' => $request->dob,
            'address' => $request->address,
            'balance' => 0.00,
        ]);

        $token = $user->createToken('mlmapp')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('sponsor_id', $request->user_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid sponsor ID or password'], 401);
        }

        $token = $user->createToken('mlmapp')->accessToken;

        return response()->json(['token' => $token], 200);
    }
}
