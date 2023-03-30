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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caixa_id');
            $table->foreign('caixa_id')->references('id')->on('caixas');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->float('opening')->default(0);
            $table->float('cash')->default(0);
            $table->float('card')->default(0);
            $table->float('orders_values')->default(0);
            $table->float('total')->default(0);
            $table->text('observation_opining')->nullable();
            $table->text('observation_close')->nullable();
            $table->string('state');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
