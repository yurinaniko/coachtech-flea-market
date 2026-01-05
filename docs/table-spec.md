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

## favorites

- お気に入り情報
- users × items の中間テーブル
- **(user_id, item_id) に複合ユニーク制約あり**

## categories / category_item

- 商品カテゴリ管理
- items とは多対多の関係

## comments

- 商品へのコメント管理
