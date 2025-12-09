<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $fillable = ['condition'];

    // Condition 1 : 多 Item
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
