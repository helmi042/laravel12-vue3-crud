<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Inertia\Inertia;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallets = auth()->user()
            ->wallets()
            ->latest()
            ->get();

        return Inertia::render('wallets/Index', [
            'wallets' => $wallets,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('wallets/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWalletRequest $request)
    {
        $request->user()->wallets()->create($request->validated());

        return redirect()->route('wallets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        return Inertia::render('wallets/Show', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        return Inertia::render('wallets/Edit', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        $wallet->update($request->validated());

        return redirect()->route('wallets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        $wallet->delete();

        return redirect()->route('wallets.index');
    }
}
