<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class WalletBalanceService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function summarize(User $user, ?Collection $wallets = null): Collection
    {
        $wallets = $wallets ?? $user->wallets()->get();

        [$changes, $lastActivity] = $this->buildMaps($user);

        return $wallets->map(function (Wallet $wallet) use ($changes, $lastActivity) {
            $delta = $changes[$wallet->id] ?? 0;
            $currentBalance = (int) $wallet->balance + $delta;
            $activity = $lastActivity[$wallet->id] ?? $wallet->updated_at;

            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'type' => $wallet->type,
                'description' => $wallet->description,
                'balance' => (int) $wallet->balance,
                'current_balance' => $currentBalance,
                'logo_url' => $wallet->logo_url,
                'updated_at' => $wallet->updated_at?->toDateString(),
                'last_activity' => $activity?->toDateString(),
            ];
        });
    }

    /**
     * @return array{0: array<int, int>, 1: array<int, Carbon>}
     */
    private function buildMaps(User $user): array
    {
        $changes = [];
        $lastActivity = [];

        $user->transactions()
            ->get(['id', 'type', 'amount', 'date', 'wallet_id', 'wallet_from_id', 'wallet_to_id'])
            ->each(function (Transaction $transaction) use (&$changes, &$lastActivity) {
                $amount = (int) $transaction->amount;
                $date = $transaction->date;

                if ($transaction->type === 'income') {
                    $this->applyChange($changes, $transaction->wallet_id, $amount);
                    $this->touchActivity($lastActivity, $transaction->wallet_id, $date);
                    return;
                }

                if ($transaction->type === 'expense') {
                    $this->applyChange($changes, $transaction->wallet_id, -$amount);
                    $this->touchActivity($lastActivity, $transaction->wallet_id, $date);
                    return;
                }

                if ($transaction->type === 'transfer') {
                    $this->applyChange($changes, $transaction->wallet_from_id, -$amount);
                    $this->touchActivity($lastActivity, $transaction->wallet_from_id, $date);

                    $this->applyChange($changes, $transaction->wallet_to_id, $amount);
                    $this->touchActivity($lastActivity, $transaction->wallet_to_id, $date);
                }
            });

        return [$changes, $lastActivity];
    }

    private function applyChange(array &$changes, ?int $walletId, int $delta): void
    {
        if (!$walletId) {
            return;
        }

        $changes[$walletId] = ($changes[$walletId] ?? 0) + $delta;
    }

    private function touchActivity(array &$activity, ?int $walletId, ?Carbon $date): void
    {
        if (!$walletId || !$date) {
            return;
        }

        $current = $activity[$walletId] ?? null;

        if (!$current || $date->greaterThan($current)) {
            $activity[$walletId] = $date;
        }
    }
}
