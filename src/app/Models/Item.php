<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'name',
    'brand',
    'condition_id',
    'description',
    'price',
    'img_url',
    ];

    public function getFullImagePathAttribute()
    {
        return str_contains($this->img_url, 'images/')
        ? $this->img_url
        : 'images/' . $this->img_url;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function buyers()
    {
        return $this->belongsToMany(User::class, 'purchases')->withTimestamps();
    }
}
