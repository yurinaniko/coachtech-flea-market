<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function index($itemId)
    {
    $item = Item::findOrFail($itemId);
    $user = Auth::user();
    $address = $user->address;

    return view('purchase.index', compact('item', 'user','address'));
    }

    public function store(Request $request, $itemId)
    {
        $address = Auth::user()->address;

        if (!$address) {
        return redirect()->route('address.edit')
                        ->with('error', '先に住所を登録してください。');
        }
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'price'   => Item::findOrFail($itemId)->price,
            'status'  => 'paid',
            'payment_method' => $request->payment_method,
            'address_id' => Auth::user()->address_id,
        ]);

        return redirect()->route('items.show', $itemId)
        ->with('success', '購入が完了しました。');
    }
}
