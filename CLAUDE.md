# CLAUDE.md

coachtechフリマ（模擬案件）で作業するときの前提。

## 応答

日本語で応答する。

## このリポジトリの形

Laravel のアプリ本体は **`src/` の下**にある。リポジトリのルートではない。
`artisan` も `composer.json` も `src/` の中。パスは `src/app/...` のように `src/` から書く。

## 動かし方

Docker Compose で動く。**Sail は使っていない。**

```bash
docker compose up -d --build     # 起動
docker compose exec php bash     # PHP コンテナに入る
```

`artisan` と `composer` は、コンテナの中で実行する。

サービス名は `nginx` / `php` / `mysql` / `phpmyadmin` / `mailhog`。

## テスト

機能のテストは `src/tests/Feature/` に置く。

```bash
docker compose exec php php artisan test
```

- テスト用 DB は **`demo_test`**。事前に MySQL 上に作っておく必要がある
- `src/.env.testing` が要る（`.env.testing.example` からコピーして `key:generate`）
- 実装を変えたら、テストを回して通るところまで直す

## CI（`.github/workflows/ci.yml`）

- `test` ジョブ: PHP 8.3 + MySQL 8.0 で `php artisan test`
- `audit` ジョブ: `composer audit --locked --ignore-unreachable`（**厳格モード**。`|| true` で握りつぶさない）
- **アクションはコミット SHA で固定している。タグ指定に戻さない**
- `concurrency` で古い実行をキャンセル、`timeout-minutes` で打ち切る。外さない

## 知らずに変えると壊れるところ

### 購入まわり

- `purchases` の **`(user_id, item_id)` 複合ユニーク**が、二重売買を防ぐ最後の砦。外さない
- `PurchaseController@store` は**行ロック（`lockForUpdate`）とユニーク制約の二段構え**で
  「確認 → 作成」をアトミックにしている。片方だけ外さない
- **自己購入の遮断（`abort_if($item->user_id === Auth::id(), 403)`）は
  `index` と `store` の両方に要る。** 画面のボタンを消すだけでは URL 直打ちを止められない
- `purchases.status`（支払い状況）と `purchases.is_completed`（取引全体の完了）は**役割が違う**。
  詳細は `docs/table-spec.md`。片方で代用しない

### Stripe Webhook

- `src/app/Http/Middleware/VerifyCsrfToken.php` の `'stripe/webhook'` 除外は**必要**。
  外すと本番で 419 になり、決済完了イベントが処理されない。署名検証はコントローラ側で行う

### 認可

ルートは3層になっている（`src/routes/web.php`）。

| 層 | 対象 |
|---|---|
| 制限なし | 商品一覧・商品詳細・Stripe Webhook |
| `auth` のみ | プロフィール登録・編集 |
| `auth` + `verified` | マイページ・出品・購入・いいね・コメント・チャット |

**プロフィール系だけ `verified` を外しているのは意図的。**
メール認証後に `profile.create` へリダイレクトする流れのため。勝手に揃えない。

## 秘密の値

- **`src/.env` は読まない。** Stripe・DB・メールの実キーが入る
- `.gitignore` は `.env` と `.env.*` を除外し、`.env.example` と `.env.testing.example` だけ通す。
  この形を崩さない
- 秘密の値を依頼文やコミットメッセージに貼らない

## 設計資料の在りか

| ファイル | 中身 |
|---|---|
| `docs/er.png` | ER図（9テーブル、PK/FK/UK つき） |
| `docs/table-spec.md` | テーブル仕様。`status` と `is_completed` の書き分けはここ |
| `README.md`「工夫した点」「セキュリティ設計で工夫した点」 | **なぜそう作ったか** |

## 作業の進め方

- **作業1つにつきブランチ1本。** `main` へ直接コミットしない
- 実装を変えたら、テストを回して結果を報告する
- 入力チェックのエラー文言は**日本語**で出す（`src/resources/lang/ja/validation.php`）
- 既存の書き方に合わせる。新しいパッケージを勝手に足さない
