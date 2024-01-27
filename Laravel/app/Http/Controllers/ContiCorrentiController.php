<?php

namespace App\Http\Controllers;

use App\Models\ContiCorrenti;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ContiCorrentiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $user = $request->user();
        $contiCreati = $user->conticorrenteOwner->append(['iban', 'owner_name', 'balance']);
        $contiJoint = $user->contiCorrentiManaged->append(['iban', 'owner_name', 'balance'])->makeHidden('pivot');

        //$allConti = $contiCreati->merge($contiJoint);

        return response()->json([ 'owned' => $contiCreati, 'joined' => $contiJoint, 'count' => $contiJoint->count() + $contiCreati->count() ]);

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
        $contiCorrenti->append(['iban', 'owner_name', 'balance', 'cointestatari'])->makeHidden(['owner', 'joints']);
        $contiCorrenti->transactionsFrom = $contiCorrenti->transactionsFrom()->get()->makeHidden(['to', 'from', 'type'])->append(['toDetails', 'typeDetails']);
        $contiCorrenti->transactionsTo = $contiCorrenti->transactionsTo()->get()->makeHidden(['to', 'from', 'type'])->append(['fromDetails', 'typeDetails']);
        return $contiCorrenti;
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
            'confirm' => 'required',
            'password' => 'required|string',
            'cointestatari' => 'string',
        ]);

        if($request->confirm != "y")
            return response()->json(['message' => 'Devi confermare l\'operazione'], 403);

        if(AuthController::confermaOperazioneByPassword($request->user(), $request->password))
        {
            // Verifica se l'utente ha già creato due istanze di ContoCorrente
            if ($request->user()->conticorrenteOwner()->count() >= 2) {
                return response()->json(['message' => 'Hai già creato il numero massimo di conti correnti.'], 403);
            }
            $newAccount = $this->create($request->user()->id);

            $emails = explode(',', $request->input('cointestatari'));

            foreach ($emails as $email) {
                $email = trim($email); // Rimuove eventuali spazi prima e dopo l'email

                if ($email != $request->user()->email) {
                    $userJ = User::where('email', $email)->first();

                    if ($userJ) {
                        $newAccount->joints()->attach($userJ->id);
                    }
                }
            }

            TransactionController::create($newAccount->id, 1, 1000, "Welcome transaction", 0, 1);
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
        return (int)substr($iban, 9);
    }

    public function apiGetContoById(Request $request)
    {
        $request->validate([
            'contoCorrenteId' => ['required','integer', 'min:0','max:4294967295', Rule::exists('conti_correntis', 'id'),]
        ]);

        $account = ContiCorrenti::find($request->contoCorrenteId);
        if(!$account->isOwnerOrJoint($request->user()->id))
            return response()->json([ 'message' =>'Non hai le autorizzazioni' ], 401);
        $account->isOwner = $account->owner === $request->user()->id;

        return response()->json(['result' => $this->show($account)]);
    }

}
