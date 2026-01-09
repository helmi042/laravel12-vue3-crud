<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'category',
        'category_id',
        'amount',
        'notes',
        'user_id',
        'wallet_id',
        'wallet_from_id',
        'wallet_to_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function walletFrom()
    {
        return $this->belongsTo(Wallet::class, 'wallet_from_id');
    }

    public function walletTo()
    {
        return $this->belongsTo(Wallet::class, 'wallet_to_id');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }
}
