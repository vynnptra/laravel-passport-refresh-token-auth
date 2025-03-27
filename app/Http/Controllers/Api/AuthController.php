<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'User has been registered successfully.',
                'data' => $user,
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error creating user',
                'error' => $th->getMessage()
            ], 500);

        }
    }

    public function login(Request $request)
    {
        try {
            $tokenRequest = $request->create('/oauth/token', 'POST', [
                'grant_type' => 'password',
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => ''
            ]);
            
            $response = app()->handle($tokenRequest);
    
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'User has been logged in successfully.',
                'data' => json_decode($response->getContent(), true),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error logging in user',
                'error' => $th->getMessage()
            ],500);
        }
    }

    public function profile()
    {
        try { 

            $user = auth()->user();
    
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Authenticated use info.',
                'data' => $user,
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'statusCode' => 401,
                'message' => 'Unauthorized.',
            ], 401);

        }
    }

    public function refreshToken(Request $request)
    {
       try {
        $tokenRequest = $request->create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'scope' => ''
        ]);

        $response = app()->handle($tokenRequest);

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Relogin successfully.',
            'data' => json_decode($response->getContent(), true),
        ], 200);

       } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthorized.',
            ], 401);
       }
    }

    public function logout(Request $request){
        try {
            $user = Auth::user();
            $user->tokens()->delete();
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'User logged out successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error logging out user',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
