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
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->foreign('transfer_id')->references('id')->on('transfers');
            $table->unsignedBigInteger('produtos_id');
            $table->foreign('produtos_id')->references('id')->on('produtos');
            $table->unsignedBigInteger('armagen_id');
            $table->foreign('armagen_id')->references('id')->on('armagens');
            $table->integer('quantity')->default(1);
            $table->float('priceSold')->default(0);
            $table->float('spent')->default(0);
            $table->float('final_price')->default(0);
            $table->float('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
    }
};
