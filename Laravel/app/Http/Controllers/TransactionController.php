<?php

namespace App\Http\Controllers;

use App\Models\ContiCorrenti;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($to,$from,$value, $reason,$fee, $type)
    {
        $t = new Transaction();
        $t->to = $to;
        $t->from = $from;
        $t->value = $value;
        $t->reason = $reason;
        $t->fee = $fee;
        $t->type = $type;
        $t->save();
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
    public function show(Transaction $transaction)
    {
        return $transaction;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public static function getTransactionsForContoCorrente($contoCorrenteId)
    {
        return Transaction::where('from', $contoCorrenteId)
            ->orWhere('to', $contoCorrenteId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function isOwnerOrJointOfTransaction(int $userId, int $transactionId): bool
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            // Se la transazione non esiste, considerala non accessibile all'utente.
            return false;
        }

        $contoCorrenteFrom = $transaction->from()->first();
        $contoCorrenteTo = $transaction->to()->first();

        if (
            $contoCorrenteFrom && $contoCorrenteFrom->isOwnerOrJoint($userId)
            || $contoCorrenteTo && $contoCorrenteTo->isOwnerOrJoint($userId)
        ) {
            return true;
        }

        return false;
    }

    public function apiGetAllTransactionsByAccount(Request $request)
    {
        $request->validate([
            'contoCorrenteId' => ['required','integer', 'min:0','max:4294967295', Rule::exists('conti_correntis', 'id'),]
        ]);
        $account = ContiCorrenti::find($request->contoCorrenteId);
        if(!$account->isOwnerOrJoint($request->user()->id))
            return response()->json([ 'message' =>'Non hai le autorizzazioni' ], 401);

        return self::getTransactionsForContoCorrente($account->id);
    }

    public function apiGetTransactionById(Request $request)
    {
        $request->validate([
            'transactionId' => ['required','integer', 'min:0','max:4294967295', Rule::exists('transactions', 'id'),]
        ]);
        if(!self::isOwnerOrJointOfTransaction($request->user()->id, $request->transactionId))
            return response()->json([ 'message' =>'Non hai le autorizzazioni' ], 401);

        return $this->show(Transaction::find($request->transactionId));

    }

    public function apiMakeTransaction(Request $request)
    {
        $request->validate([
            'iban' => 'required|string',
            'contoCorrenteId' => ['required','integer', 'min:0','max:4294967295', Rule::exists('conti_correntis', 'id')],
            'value' => 'required|double|min:0',
            'reason' => 'required|string|max:180',
            'type' => [ 'required',  'integer',  Rule::exists('transaction_types', 'id'), ],
        ]);
        $account = ContiCorrenti::find($request->contoCorrenteId);
        if(!$account->isOwnerOrJoint($request->user()->id))
            return response()->json([ 'message' =>'Non hai le autorizzazioni' ], 401);
        $fee = mt_rand() / mt_getrandmax() * ($request->value * 0.02);
        if($account->balance < $request->value + $fee)
            return response()->json([ 'message' =>'Fondi insufficienti' ], 401);
        $to = ContiCorrenti::find(ContiCorrentiController::getAccountIdByIban($request->iban));
        if(!$to)
            return response()->json([ 'message' =>'Iban non esistente' ], 401);
        $this->create($to->id, $request->contoCorrenteId, $request->value, $request->reason, $fee, $request->type);
        return response()->json([ 'status' => 'ok' ]);
    }
}
