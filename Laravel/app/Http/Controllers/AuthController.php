<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string',
            'cognome' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'id_card' => 'required|file|mimes:pdf|max:2048',
        ]);

        $filePath = $request->file('id_card')->storeAs('pdf_directory', $request->file('id_card')->getClientOriginalName());

        $user = User::create([
            'nome' => $request->nome,
            'cognome' => $request->cognome,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'id_card' => $filePath,
        ]);

        $token = $user->createToken('registration-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
