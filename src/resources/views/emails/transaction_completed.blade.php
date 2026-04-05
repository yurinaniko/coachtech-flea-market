<p>{{ $purchase->item->user->name }}さんの{{ $purchase->item->name }}が購入され取引完了しました。</p>
<p>{{ $partnerName }} さんから評価が届いています。</p>

<p>マイページの取引中の商品から取引完了の評価してください。</p>

<p>商品名：{{ $purchase->item->name }}</p>

<p>{{ $partnerName }} さんからの評価：{{ $purchase->buyer_reviewed ?? $purchase->seller_reviewed }}</p>

<p>あなたの評価終了後取引完了となります。</p>

