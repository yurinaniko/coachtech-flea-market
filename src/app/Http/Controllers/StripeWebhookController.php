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
        // コンビニ払いは checkout.session.completed の時点ではまだ未入金
        // （payment_status = unpaid）で届く。実際の入金は
        // checkout.session.async_payment_succeeded で通知される。
        // type だけで completed にすると、未入金の注文が「決済完了」になる。
        $paidEvents = [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded',
        ];
        if (in_array($event->type, $paidEvents, true)) {
            $session = $event->data->object;
            $purchaseId = $session->metadata->purchase_id ?? null;
            $paymentStatus = $session->payment_status ?? null;

            // 入金が確認できたものだけ completed にする（未入金は pending のまま）。
            if ($purchaseId && $paymentStatus === 'paid') {
                Purchase::where('id', $purchaseId)
                    ->update([
                        'status' => 'completed',
                    ]);
            }
        }
        return response()->json(['status' => 'success']);
    }
}
