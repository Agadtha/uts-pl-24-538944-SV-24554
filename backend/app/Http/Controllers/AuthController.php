<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        // Hapus token yang sedang dipakai saja
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.'], 200);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()], 200);
    }
}
