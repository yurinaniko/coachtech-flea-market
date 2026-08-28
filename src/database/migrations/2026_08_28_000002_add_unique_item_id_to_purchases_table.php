<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 二重売買（別ユーザーが同一商品を同時購入）を防ぐDBの最後の砦。
// アプリ側のロック確認をすり抜けても、item_id のユニーク制約でINSERTが失敗する。
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unique('item_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique(['item_id']);
        });
    }
};
