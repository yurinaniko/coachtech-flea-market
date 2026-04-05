<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('price');
            $table->string('status', 50)->default('purchased');
            $table->string('payment_method', 50);
            $table->string('sending_postcode', 8);
            $table->string('sending_address', 255);
            $table->string('sending_building', 255)->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('buyer_reviewed')->nullable();
            $table->integer('seller_reviewed')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'item_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
