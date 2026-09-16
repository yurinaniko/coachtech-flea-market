<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 売り切れかどうかは purchases に購入レコードがあるかで判定する（ItemController@index が
        // with('purchase') で読んでいる）。items に is_sold のような列は持たない。
        if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;
        $purchaseId = $session->metadata->purchase_id ?? null;
            if ($purchaseId) {
            Purchase::where('id', $purchaseId)
                ->update([
                'status' => 'completed',
                ]);
            }
        }
        return response()->json(['status' => 'success']);
    }
}
