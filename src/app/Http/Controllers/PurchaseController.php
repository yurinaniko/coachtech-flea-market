<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    public function index(Item $item, Request $request)
    {
        $user = Auth::user()->fresh();
        // ① 支払い方法が来ていたらセッションに保存
        if ($request->filled('payment_method')) {
        session(['payment_method' => $request->payment_method]);
        }

        // ② 表示用はセッションから取得
        $selectedMethod = session('payment_method', '');

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

        $purchase = Purchase::firstOrCreate(
        [
            'user_id' => Auth::id(),
            'item_id' => $item->id,
        ],
        [
            'price'            => $item->price,
            'status'           => 'pending',
            'payment_method'   => $validated['payment_method'],
            'sending_postcode' => $validated['postal_code'],
            'sending_address'  => $validated['address'],
            'sending_building' => $validated['building'],
        ]
        );

        session(['purchase_id' => $purchase->id]);

        return redirect()->route('purchase.checkout');
    }

    public function checkout(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $purchaseId = session('purchase_id');
        $purchase = Purchase::findOrFail($purchaseId);
        $item = $purchase->item;
        $paymentMethod = $purchase->payment_method;

        if ($paymentMethod === 'card') {
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'purchase_id' => $purchase->id,
                'item_id' => $item->id,
            ],

            'success_url' => route('purchase.result', ['status' => 'success'], true),
            'cancel_url'  => route('purchase.result', ['status' => 'cancel'], true),
        ]);

            return redirect($session->url);
        }

        if ($paymentMethod === 'konbini') {
            $session = StripeSession::create([
                'payment_method_types' => ['konbini'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',

                // ★ 24時間未満にする（重要）
                'expires_at' => now()->addHours(23)->timestamp,

                'metadata' => [
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                ],
                'success_url' => route('purchase.result', ['status' => 'success'], true),
                'cancel_url'  => route('purchase.result', ['status' => 'cancel'], true),
                ]);

            return redirect($session->url);
        }
            abort(400);
    }

    public function result(Request $request)
    {
        $status = $request->query('status'); // success or cancel

        return view('purchase.result', compact('status'));
    }
}
