<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = $request->user()
            ->transactions()
            ->with(['wallet', 'walletFrom', 'walletTo', 'transactionCategory'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Transaction $transaction) => [
                'id' => $transaction->id,
                'date' => $transaction->date->toDateString(),
                'type' => $transaction->type,
                'category' => $transaction->transactionCategory?->name ?? $transaction->category,
                'amount' => $transaction->amount,
                'notes' => $transaction->notes,
                'walletId' => $transaction->wallet_id,
                'walletFromId' => $transaction->wallet_from_id,
                'walletToId' => $transaction->wallet_to_id,
                'walletName' => $transaction->wallet?->name,
                'walletFromName' => $transaction->walletFrom?->name,
                'walletToName' => $transaction->walletTo?->name,
            ]);

        $wallets = $request->user()
            ->wallets()
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = $request->user()
            ->transactionCategories()
            ->orderBy('type')
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'wallets' => $wallets,
            'transactionCategories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();

        if ($data['type'] === 'transfer') {
            $data['wallet_id'] = null;
            $data['category_id'] = null;
            $data['category'] = 'Transfer antar dompet';
        } else {
            $category = $request->user()
                ->transactionCategories()
                ->findOrFail($data['category_id']);

            $data['category'] = $category->name;
            $data['wallet_from_id'] = null;
            $data['wallet_to_id'] = null;
        }

        $request->user()->transactions()->create($data);

        return redirect()->route('transactions.index');
    }
}
