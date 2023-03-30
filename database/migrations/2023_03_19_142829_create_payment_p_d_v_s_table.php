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
        Schema::create('payment_p_d_v_s', function (Blueprint $table) {
            $table->id();
            $table->integer('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')->delete('CASCADE');
            $table->integer('order_pos_id');
            $table->foreign('order_pos_id')->references('id')->on('order_pos');
            $table->integer('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->float('amountPaid');
            $table->float('change')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_p_d_v_s');
    }
};
