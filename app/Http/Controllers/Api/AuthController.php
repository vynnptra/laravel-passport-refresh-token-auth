<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

            $response = Http::post(env('APP_URL') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSPORT_PASSWORD_SECRET'),
                'username' => $user['email'],
                'password' => $user['password'],
                'scope' => '',
            ]);
            $user['token'] = $response->json();
    
            return response()->json([
                'success' => true,
                'statusCode' => 201,
                'message' => 'User has been registered successfully.',
                'data' => $user,
            ], 201);

        } catch (\Throwable $th) {

            return response()->json([
                'message' => 'Error creating user',
                'error' => $th->getMessage()
            ], 500);

        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $tokenResult = $user->createToken('Personal Access Token');
        
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'refresh_token' => $tokenResult->token->id,
            ]
        ]);
    }

    public function profile()
    {
        if (Auth::check()) {
            $user = auth()->user();
    
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Authenticated use info.',
                'data' => $user,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'statusCode' => 401,
            'message' => 'Unauthorized.',
        ], 401);
    }

    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required'
        ]);

        $token = \Laravel\Passport\Token::find($request->refresh_token);
        
        if (!$token || $token->revoked) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token'
            ], 401);
        }

        $token->revoke();

        $user = $token->user;
        $newToken = $user->createToken('Personal Access Token');

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $newToken->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $newToken->token->expires_at,
                'refresh_token' => $newToken->token->id,
            ]
        ], 200);
    }

    public function logout(){
        try {
            $user = Auth::user();
            $user->tokens()->delete();
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
