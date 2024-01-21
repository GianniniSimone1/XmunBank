<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string',
            'cognome' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nome' => $request->nome,
            'cognome' => $request->cognome,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('registration-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user->tokens()->delete(); // Invalida tutti i token esistenti per l'utente
            $token = $user->createToken('login-token')->plainTextToken;

            return response()->json(['token' => $token]);
        }

        // Autenticazione fallita
        return response()->json([
            'email' => ['Le credenziali non sono corrette.'],
        ], 401);
    }

}
