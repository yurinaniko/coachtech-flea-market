<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function store($itemId)
    {
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'price'   => Item::findOrFail($itemId)->price,
            'status'  => 'paid',
        ]);

        return redirect()->back();
    }
}
