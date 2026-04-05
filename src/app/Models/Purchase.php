<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'price',
        'status',
        'payment_method',
        'sending_postcode',
        'sending_address',
        'sending_building',
        'is_completed',
        'buyer_reviewed',
        'seller_reviewed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
