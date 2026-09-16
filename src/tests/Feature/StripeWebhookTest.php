<?php

namespace Tests\Feature;

use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Stripe Webhook（POST /stripe/webhook）の受け入れ判定。
 *
 * このエンドポイントは CSRF 除外なので、正しさの拠り所は署名検証だけになる。
 * あわせて「入金が確認できたものだけ支払い済みにする」ことを担保する。
 *
 * コンビニ払いは checkout.session.completed が“未入金のまま”届き
 * （payment_status = unpaid）、実際の入金は async_payment_succeeded で通知される。
 * type だけを見て completed にすると、入金前の注文が決済完了になってしまう。
 * （status = 支払い状況 / is_completed = 取引完了 の書き分けは docs/table-spec.md）
 */
class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.stripe.webhook_secret' => self::SECRET]);
    }

    /** 署名付きで Webhook を送る（$signingSecret を変えると署名不正を再現できる） */
    private function webhookを送る(array $payload, string $signingSecret = self::SECRET): TestResponse
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $body, $signingSecret);

        return $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => 't=' . $timestamp . ',v1=' . $signature,
            ],
            $body
        );
    }

    /** Stripe から届くイベント本文の最小形 */
    private function イベント(string $type, int $purchaseId, string $paymentStatus): array
    {
        return [
            'id' => 'evt_test',
            'object' => 'event',
            'type' => $type,
            'data' => [
                'object' => [
                    'id' => 'cs_test',
                    'object' => 'checkout.session',
                    'payment_status' => $paymentStatus,
                    'metadata' => ['purchase_id' => (string) $purchaseId],
                ],
            ],
        ];
    }

    /** @test */
    public function 署名が正しくないWebhookは拒否され支払い状況は変わらない()
    {
        $purchase = Purchase::factory()->create(['status' => 'pending']);

        $this->webhookを送る(
            $this->イベント('checkout.session.completed', $purchase->id, 'paid'),
            'whsec_wrong_secret'
        )->assertStatus(400);

        $this->assertSame('pending', $purchase->fresh()->status);
    }

    /** @test */
    public function コンビニ払いの未入金通知では支払い済みにならない()
    {
        $purchase = Purchase::factory()->create([
            'status' => 'pending',
            'payment_method' => 'konbini',
        ]);

        $this->webhookを送る(
            $this->イベント('checkout.session.completed', $purchase->id, 'unpaid')
        )->assertOk();

        $this->assertSame('pending', $purchase->fresh()->status);
    }

    /** @test */
    public function 入金済みの通知で支払い済みになる()
    {
        $purchase = Purchase::factory()->create(['status' => 'pending']);

        $this->webhookを送る(
            $this->イベント('checkout.session.completed', $purchase->id, 'paid')
        )->assertOk();

        $this->assertSame('completed', $purchase->fresh()->status);
    }

    /** @test */
    public function コンビニ入金後の非同期通知で支払い済みになる()
    {
        $purchase = Purchase::factory()->create([
            'status' => 'pending',
            'payment_method' => 'konbini',
        ]);

        $this->webhookを送る(
            $this->イベント('checkout.session.async_payment_succeeded', $purchase->id, 'paid')
        )->assertOk();

        $this->assertSame('completed', $purchase->fresh()->status);
    }
}
