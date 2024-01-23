<?php

namespace App\Http\Controllers;

use App\Models\ContiCorrenti;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContiCorrentiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //TODO qui non funziona correttamente
        $user = $request->user();
        return response()->json(ContiCorrenti::where('owner', $user->id));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($owner)
    {
        $newAccount = new ContiCorrenti();
        $newAccount->owner = $owner;
        $newAccount->save();
        $newAccount->append(['iban', 'owner_name', 'balance']);
        return $newAccount;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ContiCorrenti $contiCorrenti)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContiCorrenti $contiCorrenti)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContiCorrenti $contiCorrenti)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContiCorrenti $contiCorrenti)
    {
        //
    }

    public function apiCreateAccount(Request $request)
    {
        $request->validate([
            'confirm' => 'required|boolean',
            'password' => 'required|string',
        ]);

        if(AuthController::confermaOperazioneByPassword($request->user(), $request->password))
        {
            // Verifica se l'utente ha già creato due istanze di ContoCorrente
            if ($request->user()->conticorrenteOwner()->count() >= 2) {
                return response()->json(['message' => 'Hai già creato il numero massimo di conti correnti.'], 403);
            }
            $newAccount = $this->create($request->user()->id);
            return response()->json([$newAccount, 'status' => 'ok']);
        }
        else
            return response()->json([ 'message' =>'Password errata' ], 401);
    }

    public function addJoint(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email'),
            ],
            'contoCorrenteId' => ['required','integer', 'min:0','max:4294967295', Rule::exists('conti_correntis', 'id'),]
        ]);

        $user = $request->user();
        $contoCorrente = ContiCorrenti::find($request->contoCorrenteId);
        if(!$this->isOwner($user->id, $request->contoCorrenteId))
            return response()->json([ 'message' => 'Non hai le autorizzazioni' ], 401);

        $joiner = User::firstWhere('email', $request->email);

        $contoCorrente->joints()->syncWithoutDetaching($joiner->id);
        return response()->json([ 'status' => 'ok' ]);
    }

    public static function isOwner($userId, $contoCorrenteId)
    {
        $contoCorrente = ContiCorrenti::find($contoCorrenteId);
        if(!$contoCorrente)
            return false;
        elseif ($contoCorrente->owner == $userId)
            return true;
        else
            return false;
    }
    public static function getAccountIdByIban($iban)
    {
        return (int) substr($iban, 5);
    }

}
