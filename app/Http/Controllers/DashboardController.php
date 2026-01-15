<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\WalletBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth]);

        $incomeTotal = (clone $baseMonthlyQuery)->where('transactions.type', 'income')->sum('transactions.amount');
        $expenseTotal = (clone $baseMonthlyQuery)->where('transactions.type', 'expense')->sum('transactions.amount');
        $transferTotal = (clone $baseMonthlyQuery)->where('transactions.type', 'transfer')->sum('transactions.amount');

        $incomeCount = (clone $baseMonthlyQuery)->where('transactions.type', 'income')->count();
        $expenseCount = (clone $baseMonthlyQuery)->where('transactions.type', 'expense')->count();
        $transferCount = (clone $baseMonthlyQuery)->where('transactions.type', 'transfer')->count();

        $dailySummary = (clone $baseMonthlyQuery)
            ->selectRaw('transactions.date as date,
                IFNULL(SUM(CASE WHEN transactions.type = "income" THEN transactions.amount ELSE 0 END), 0) as income_total,
                IFNULL(SUM(CASE WHEN transactions.type = "expense" THEN transactions.amount ELSE 0 END), 0) as expense_total')
            ->groupBy('transactions.date')
            ->orderBy('transactions.date')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::parse($row->date)->toDateString(),
                'income' => (float) $row->income_total,
                'expense' => (float) $row->expense_total,
                'net' => (float) $row->income_total - (float) $row->expense_total,
            ]);

        $topexpenseSummary = (clone $baseMonthlyQuery)
            ->where('transactions.type', 'expense')
            ->leftJoin('transaction_categories', 'transactions.category_id', '=', 'transaction_categories.id')
            ->selectRaw('COALESCE(transaction_categories.name, transactions.category) as category_name, SUM(transactions.amount) as total_amount')
            ->groupBy('category_name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category_name ?? 'Tidak ada kategori',
                'total' => (float) $row->total_amount,
            ]);

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
            'chartData' => $dailySummary,
            'topExpenses' => $topexpenseSummary,
        ]);
    }
}
