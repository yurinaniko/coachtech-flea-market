<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Purchase;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    public function index(Item $item, Request $request)
    {
        // 出品者本人は自分の商品の購入画面を開けない（サーバー側で遮断）
        abort_if($item->user_id === Auth::id(), 403, 'You cannot purchase your own item.');

        $user = Auth::user()->fresh();
        if (session('current_item_id') !== $item->id) {
        session()->forget('payment_method');
        }
        if ($request->has('payment_method')) {
            if ($request->filled('payment_method')) {
            session(['payment_method' => $request->payment_method]);
            } else {
            session()->forget('payment_method');
            }
        }
        $selectedMethod = session('payment_method', '');
        session(['current_item_id' => $item->id]);
        return view('purchase.index', [
            'item' => $item,
            'user' => $user,
            'selectedMethod' => $selectedMethod,
        ]);
    }

    public function store(PurchaseRequest $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        // 出品者本人は自分の商品を購入できない（フロントのボタン非表示だけに頼らずサーバー側で遮断）
        abort_if($item->user_id === Auth::id(), 403, 'You cannot purchase your own item.');

        $validated = $request->validated();

        // 同一商品の二重売買を防ぐ。行ロックで「確認→作成」をアトミックにし、
        // すり抜けても purchases のユニーク制約（DBの最後の砦）で弾く。
        try {
            $purchase = DB::transaction(function () use ($item, $validated) {
                $locked = Item::whereKey($item->id)->lockForUpdate()->first();

                $alreadyPurchased = Purchase::where('item_id', $locked->id)
                    ->whereIn('status', ['pending', 'completed'])
                    ->exists();
                abort_if($alreadyPurchased, 403, 'This item has already been purchased.');

                return Purchase::create([
                    'user_id'          => Auth::id(),
                    'item_id'          => $locked->id,
                    'price'            => $locked->price,
                    'status'           => $validated['payment_method'] === 'konbini'
                                            ? 'pending'
                                            : 'completed',
                    'is_completed'     => false,
                    'payment_method'   => $validated['payment_method'],
                    'sending_postcode' => $validated['postal_code'],
                    'sending_address'  => $validated['address'],
                    'sending_building' => $validated['building'],
                ]);
            });
        } catch (QueryException $e) {
            // ユニーク制約違反＝別リクエストが先に購入を確定した（二重売買を防いだ）
            abort(403, 'This item has already been purchased.');
        }

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
        $status = $request->query('status');
        return view('purchase.result', compact('status'));
    }
}