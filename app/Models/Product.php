<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'brand', 'category_id', 'price', 'weight', 'description', 'user_id'];

    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
