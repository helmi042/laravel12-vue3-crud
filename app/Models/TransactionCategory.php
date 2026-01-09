<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
