<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('methods_payment_translates', function (Blueprint $table) {
            $table->id();
            $table->string('local');
            $table->unsignedBigInteger('methods_id');
            $table->foreign('methods_id')->references('id')->on('payment_methods');
            $table->string('translate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methods_payment_translates');
    }
};
