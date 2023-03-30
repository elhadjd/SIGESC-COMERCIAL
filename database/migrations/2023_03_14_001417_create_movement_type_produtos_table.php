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
        Schema::create('movement_type_produtos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('produtos_id')->nullable()->default(null);
            $table->foreign('produtos_id')->references('id')->on('produtos');
            $table->integer('order_pos_id')->nullable()->default(null);
            $table->foreign('order_pos_id')->references('id')->on('order_pos');
            $table->integer('movement_type_id')->nullable()->default(null);
            $table->foreign('movement_type_id')->references('id')->on('movement_types');
            $table->integer('armagen_id')->nullable()->default(null);
            $table->foreign('armagen_id')->references('id')->on('armagens');
            $table->integer('quantity')->nullable()->default(0);
            $table->integer('price_cost')->nullable()->default(0);
            $table->integer('price_sold')->nullable()->default(0);
            $table->string('motive')->nullable()->default(null);
            $table->integer('quantityAfter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movement_type_produtos');
    }
};
