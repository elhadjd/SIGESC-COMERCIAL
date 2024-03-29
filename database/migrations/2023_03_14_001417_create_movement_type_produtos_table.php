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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('produtos_id')->nullable();
            $table->foreign('produtos_id')->references('id')->on('produtos');
            $table->unsignedBigInteger('order_pos_id')->nullable();
            $table->foreign('order_pos_id')->references('id')->on('order_pos');
            $table->unsignedBigInteger('movement_type_id')->nullable();
            $table->foreign('movement_type_id')->references('id')->on('movement_types');
            $table->unsignedBigInteger('armagen_id')->default();
            $table->foreign('armagen_id')->references('id')->on('armagens');
            $table->integer('quantity')->default(0);
            $table->float('price_cost')->default(0);
            $table->float('price_sold')->default(0);
            $table->string('motive')->nullable();
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
