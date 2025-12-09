<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function index(Item $item, Request $request)
    {
        $user = Auth::user()->fresh();
        $selectedMethod = $request->payment_method ?? '';
        session(['current_item_id' => $item->id]);

        $placeholder = [
            'postal_code' => 'XXX-YYYY',
            'address'     => 'ここには住所と建物が入ります',
            'building'    => '',
        ];

        return view('purchase.index', [
            'item' => $item,
            'user' => $user,
            'selectedMethod' => $selectedMethod,
            'placeholder' => $placeholder,
        ]);
    }


    public function store(PurchaseRequest $request, $itemId)
    {
        $validated = $request->validated();
        $item = Item::findOrFail($itemId);

        Purchase::create([
            'user_id'          => Auth::id(),
            'item_id'          => $item->id,
            'price'            => $item->price,
            'status'           => 'purchased',
            'payment_method'   => $validated['payment_method'],
            'sending_postcode' => $validated['postal_code'],
            'sending_address'  => $validated['address'],
            'sending_building' => $validated['building'],
        ]);

        return redirect()->route('mypage.index');
    }
}
