<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id();
            $table->string('condition', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
}