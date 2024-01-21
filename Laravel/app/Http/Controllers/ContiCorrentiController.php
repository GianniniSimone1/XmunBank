<?php

namespace App\Http\Controllers;

use App\Models\ContiCorrenti;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContiCorrentiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json($user->contiCorrentiManaged());

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
}
