<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:8',
            'referer_id' => 'nullable|exists:users,sponsor_id',
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

        // Reward the referrer if referer_id is provided
        if ($request->has('referer_id')) {
            $referrer = User::where('sponsor_id', $request->referer_id)->first();
            if ($referrer) {
                $referrer->balance += 50; // Reward the referrer with 50 units
                $referrer->save();

                // Record the earning
                Earning::create([
                    'user_id' => $referrer->id,
                    'amount' => 50,
                    'description' => 'direct',
                ]);
            }
        }

        $token = $user->createToken('mlmapp')->accessToken;

        return response()->json(['token' => $token], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('mobile_number', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }
    public function preLogin(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 400);
        }

        return response()->json(['message' => 'Credentials are correct'], 200);
    }

    public function preRegister(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string|unique:users,mobile_number',
        ]);

        return response()->json(['message' => 'Mobile number is unique'], 200);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|exists:users,mobile_number',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('mobile_number', $request->mobile_number)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }
    public function checkMobile(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string'
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();

        if ($user) {
            return response()->json(['message' => 'Mobile number exists.'], 200);
        }

        return response()->json(['message' => 'Mobile number does not exist.'], 404);
    }

}
