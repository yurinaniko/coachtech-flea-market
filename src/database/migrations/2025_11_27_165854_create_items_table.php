<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // 商品名
            $table->integer('price');       // 価格
            $table->string('brand')->nullable();
            $table->string('image');        // 画像パス
            $table->text('description');    // 商品説明
            $table->string('condition');
            $table->unsignedBigInteger('user_id'); // 出品者ID
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
