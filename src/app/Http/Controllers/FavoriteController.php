<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
        $user = Auth::user();
        if ($item->user_id === Auth::id()) {
            return back();
        }
        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
        } else {
            $user->favorites()->attach($item->id);
        }
        return back();
    }
}

