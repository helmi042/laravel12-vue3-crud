<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\WalletBalanceService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(Request $request, WalletBalanceService $walletBalanceService)
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $baseMonthlyQuery = $request->user()
            ->transactions()
            ->whereBetween('date', [$startOfMonth, $endOfMonth]);

        $incomeTotal = (clone $baseMonthlyQuery)->where('type', 'income')->sum('amount');
        $expenseTotal = (clone $baseMonthlyQuery)->where('type', 'expense')->sum('amount');
        $transferTotal = (clone $baseMonthlyQuery)->where('type', 'transfer')->sum('amount');

        $incomeCount = (clone $baseMonthlyQuery)->where('type', 'income')->count();
        $expenseCount = (clone $baseMonthlyQuery)->where('type', 'expense')->count();
        $transferCount = (clone $baseMonthlyQuery)->where('type', 'transfer')->count();

        $wallets = $walletBalanceService
            ->summarize($request->user(), $request->user()->wallets()->orderBy('name')->get())
            ->map(fn (array $wallet) => [
                'id' => $wallet['id'],
                'name' => $wallet['name'],
                'type' => $wallet['type'],
                'balance' => $wallet['current_balance'],
                'updatedAt' => $wallet['last_activity'],
            ]);

        $recentTransactions = $request->user()
            ->transactions()
            ->with(['wallet', 'walletFrom', 'walletTo', 'transactionCategory'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (Transaction $transaction) => [
                'id' => $transaction->id,
                'date' => $transaction->date->toDateString(),
                'type' => $transaction->type,
                'category' => $transaction->transactionCategory?->name ?? $transaction->category,
                'amount' => $transaction->amount,
                'walletName' => $transaction->wallet?->name,
                'walletFromName' => $transaction->walletFrom?->name,
                'walletToName' => $transaction->walletTo?->name,
            ]);

        return Inertia::render('Dashboard', [
            'summary' => [
                'income' => $incomeTotal,
                'expense' => $expenseTotal,
                'net' => $incomeTotal - $expenseTotal,
            ],
            'transactionSummary' => [
                'income' => [
                    'total' => $incomeTotal,
                    'count' => $incomeCount,
                ],
                'expense' => [
                    'total' => $expenseTotal,
                    'count' => $expenseCount,
                ],
                'transfer' => [
                    'total' => $transferTotal,
                    'count' => $transferCount,
                ],
            ],
            'wallets' => $wallets,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
