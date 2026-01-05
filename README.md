# COACHTECH フリーマーケット（模擬案件）
COACHTECH の模擬案件として作成した
フリーマーケットアプリケーションです。
ユーザー登録（メール認証も含む）、商品出品、購入、コメント、お気に入り、
SRtripe を利用した決済機能など、課題要件を満たす機能を一通り実装しています。
## セットアップ手順

## 1 リポジトリのクローン
```bash
git clone git@github.com:yurinaniko/coachtech-flea-market.git
cd coachtech-flea-market
```
## 2 Docker 起動
```bash
docker compose up -d --build
```
## 3 PHP コンテナに入る
```bash
docker compose exec php bash
```
## 4 Composer インストール
```bash
composer install
```
## 5 .env ファイル作成
```bash
cp .env.example .env
php artisan key:generate
```
## 6 .env 設定
### ① データベース設定（Docker）
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=root
```
### ②　 アプリケーション設定
```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```
### ③　 メール設定（MailHog）
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="coachtech-flea-market"
```
### ④ Stripe（テスト環境）
```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxx
※ Stripe のキーは各自の **テスト用 API キー** を設定してください。
（本番キーは使用しません）
```
## 7 データベース初期化（マイグレーション & シーディング）
```bash
php artisan migrate:fresh --seed
```
## 8 画像表示設定
本アプリでは、以下のように画像を管理しています。
```
- 商品画像（Seeder登録・出品画像）
  → `storage/app/public/images`
- プロフィール画像
  → `storage/app/public/profile`

画像はすべて `storage/app/public` 配下に保存されるため、
初回セットアップ時には以下のコマンドを必ず実行してください。
```
```bash
php artisan storage:link
```
※ 商品画像・プロフィール画像は storage/app/public 配下に保存されるため、
GitHub 上には含まれていません。
## 9 アプリケーション確認
以下のURLにアクセスすると、アプリケーションが表示されます。
```
http://localhost:8000
```
### 認証済みユーザーの場合
会員登録・メール認証・プロフィール登録後、以下の画面表示や機能を確認できます。

- 商品一覧表示
- 商品詳細表示
- マイリスト
- お気に入り登録
- コメント投稿
- プロフィール画面
- プロフィール編集画面
- 商品出品画面
- 商品購入画面
- 住所変更画面
- Stripe決済
- 購入完了画面（カード払いの時のみ表示）

※ Seeder で登録済みのテストユーザーは、ログイン後すぐに上記機能を確認できます。

### 未認証ユーザーの場合
未認証のユーザーでも、以下の画面は閲覧可能です。

- 商品一覧表示
- 商品詳細表示

ただし、以下の画面遷移や機能利用できません。

- お気に入り機能
- コメント機能
- マイリスト
- プロフィール画面
- プロフィール編集画面
- 商品出品画面
- 商品購入画面
- 住所変更画面
- Stripe決済
- 購入完了画面（カード払いの時のみ表示）
※商品購入ボタンを押すとログイン画面に遷移されます。
※ヘッダーのログインボタンからもログインページに遷移します。

## 10 テストユーザー
Seeder により、以下のテストユーザーを作成しています。
```
Email：test@test.com

Password：password
```
※ テストを円滑に行うため、Seeder 側で メール認証済み状態 にしています。
ログイン画面からお試しください。
## 11 メール認証（MailHog）
開発環境では MailHog を使用しています。
```
アプリ： http://localhost:8000

MailHog： http://localhost:8025
```
メール認証・通知メールは MailHog 上で確認できます。
メール認証誘導画面の認証はこちらからのボタンを押すとメール認証画面（MailHog画面）に遷移されます。

## 備考
※（M1 / M2 Mac）
本プロジェクトでは、Apple Silicon（M1 / M2 Mac）環境でも
問題なく動作するよう、docker-compose.yml にて
ARM64 対応の Docker image を使用しています。
```yaml
mysql:
  image: arm64v8/mysql:8.0
  platform: linux/arm64/v8
```
そのため、M1 / M2 Mac 環境でも
追加設定なしで Docker を起動できます。
### コメント機能について
```
・商品詳細画面では、Seederにより初期コメントが表示されます。
・ログインユーザーが投稿したコメントは、既存コメントの下に時系列で追加表示されます。
・ログインしていないユーザーにはコメント入力欄は表示されますが、送信ボタンは無効化されており、コメントを追加することはできません。
```
### 商品一覧
- ログイン済みのユーザー
商品一覧ページの初期表示では、
「おすすめ」「マイリスト」いずれのタブも未選択の状態となり、
すべての商品が一覧表示されます。

- 「おすすめ」タブを選択すると、おすすめ商品一覧が表示されます。
- 「マイリスト」タブを選択すると、お気に入り登録した商品一覧が表示されます。
※売り切れ商品は一覧画面では詳細遷移不可としていますが、
URL直接アクセス等を想定し、商品詳細画面にも売り切れ表示の分岐を実装しています。

- ログインしていないユーザー
初期画面からおすすめページのみ表示されます。

### プロフィール画面
ログイン後、ヘッダーのマイページボタンを押すとプロフィール画面に遷移し、
以下の情報を確認できます。

初期表示では「出品した商品」の一覧が表示され、
「購入した商品」はタブを選択することで切り替えて確認できます。

- **出品した商品**
  自身が出品した商品の一覧が表示され、出品状況を確認できます。

- **購入した商品**
  購入済み商品の一覧が表示され、過去の購入履歴を確認できます。
## 使用技術
- 種類	バージョン
- PHP	8.x
- Laravel	8.x
- MySQL	8.0
- Nginx	1.25
- Docker / Docker Compose	最新
- Stripe	テスト環境
- MailHog	開発用
- phpMyAdmin	使用

## 機能一覧
- 機能一覧
- 会員登録 / メール認証（MailHog） / プロフィール登録（画像アップロード）
- ログイン
- プロフィール編集（画像アップロード）
- 商品出品
- 商品一覧 / 詳細表示
- お気に入り登録
- コメント投稿
- 商品購入（Stripe 決済）
- プロフィール（出品商品 / 購入商品）

## ER 図・テーブル仕様
```
ER 図：docs/er.png

テーブル仕様書：docs/table-spec.md
```
※ 詳細なカラム定義・制約はコーチテック提供の要件シートに準拠しています。
### ユニークキー設計について

Usersテーブルでは email にユニーク制約を設定しています。

一方で、FavoritesテーブルやCategory_Itemテーブルなどの中間テーブルでは、
同一ユーザーが同一商品を重複して登録できないよう、
`user_id` と `item_id` の複合ユニークキーを設定しています。

これにより、データの整合性を保ちつつ、
アプリケーション側の重複制御をシンプルにしています。

## 実現した応用機能
- メール認証(MailHog)
- Stripe Checkout 決済
- 画像アップロード（storage 管理）
- 中間テーブルによる多対多管理
- 商品一覧画面でのおすすめ、マイリストのページネーション・検索機能の保持
- phpMyAdmin（DB確認用）

## テストについて
主要な機能（認証・商品操作・検索・お気に入り・購入処理・プロフィール更新）について、Feature Test を実装しています。
正常系・異常系（バリデーション、権限制御、未ログイン時の挙動）を中心に、実際のユーザー操作を想定したテストを網羅しています。
Docker 環境上で `php artisan test` を実行し、すべてのテストが PASS することを確認しています。