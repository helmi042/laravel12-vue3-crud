<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use App\Services\WalletBalanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, WalletBalanceService $walletBalanceService)
    {
        $wallets = $walletBalanceService->summarize(
            $request->user(),
            $request->user()->wallets()->orderBy('name')->get()
        );

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
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('wallet-logos', 'public');
        }

        unset($data['logo']);

        $request->user()->wallets()->create($data);

        return redirect()->route('wallets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Wallet $wallet, WalletBalanceService $walletBalanceService)
    {
        $wallet = $request->user()->wallets()->findOrFail($wallet->id);
        $walletSummary = $walletBalanceService->summarize($request->user(), collect([$wallet]))->first();
        if (!$walletSummary) {
            abort(404);
        }

        return Inertia::render('wallets/Show', [
            'wallet' => $walletSummary,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Wallet $wallet)
    {
        $wallet = $request->user()->wallets()->findOrFail($wallet->id);

        return Inertia::render('wallets/Edit', [
            'wallet' => $wallet,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        $wallet = $request->user()->wallets()->findOrFail($wallet->id);
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($wallet->logo_path) {
                Storage::disk('public')->delete($wallet->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('wallet-logos', 'public');
        }

        unset($data['logo']);

        $wallet->update($data);

        return redirect()->route('wallets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Wallet $wallet)
    {
        $wallet = $request->user()->wallets()->findOrFail($wallet->id);

        if ($wallet->logo_path) {
            Storage::disk('public')->delete($wallet->logo_path);
        }

        $wallet->delete();

        return redirect()->route('wallets.index');
    }
}
