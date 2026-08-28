<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Stripe Webhook は外部からの署名付きPOSTでCSRFトークンを持てないため除外
        // （署名検証は StripeWebhook 側で実施）。除外しないと本番で419になり
        // 決済完了イベントが処理されない。
        'stripe/webhook',
    ];
}
