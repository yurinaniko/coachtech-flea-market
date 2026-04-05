# テーブル仕様概要

本アプリで使用している主なテーブル構成を示します。
詳細な要件定義はコーチテック提供の要件シートに準拠しています。

## users

- ユーザー情報を管理するテーブル
- email はユニーク制約あり

## profiles

- ユーザーのプロフィール情報
- users と 1 対 1 の関係

## items

- 出品された商品情報
- users（出品者）、conditions と紐づく

## purchases

- 商品購入情報
- users × items の中間的役割
- 同一ユーザーが同一商品を複数回購入できないよう
  **(user_id, item_id) に複合ユニーク制約を設定**
  - 1つの商品に対して1件の購入が発生する想定

### status と is_completed の役割について

本テーブルでは、取引の状態を以下の2つのカラムで管理する。

■ status（取引ステータス）

支払い状況を管理するカラム

pending：支払い待ち（コンビニ決済など）
completed：支払い完了（カード決済など）

※ 決済の完了状態のみを表し、評価の有無は含まない

■ is_completed（取引完了フラグ）

取引全体の完了を管理するカラム

false：評価未完了（取引継続中）
true：購入者・出品者の双方の評価が完了し、取引終了
■ 状態遷移の例
状態	         status	        is_completed
購入直後	     pending	          false
決済完了	     completed	        false
評価途中	     completed	        false
取引完了       completed	        true
（双方評価済）

■ 補足

status と is_completed は役割が異なるため、同時に管理することで

支払い状態
取引の進行状態（評価含む）

を明確に分離して管理できる

## favorites

- お気に入り情報
- users × items の中間テーブル
- **(user_id, item_id) に複合ユニーク制約あり**

## categories / category_item

- 商品カテゴリ管理
- items とは多対多の関係

## comments

- 商品コメントと取引チャットを管理するテーブル
- item_id により商品に紐づくコメントを管理する
- purchase_id が null の場合は商品コメント
- purchase_id がある場合は取引チャット