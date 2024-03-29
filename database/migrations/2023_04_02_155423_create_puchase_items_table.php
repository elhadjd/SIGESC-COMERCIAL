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
        Schema::create('puchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('puchase_id');
            $table->foreign('puchase_id')->references('id')->on('puchases');
            $table->unsignedBigInteger('produtos_id');
            $table->foreign('produtos_id')->references('id')->on('produtos');
            $table->unsignedBigInteger('armagen_id');
            $table->foreign('armagen_id')->references('id')->on('armagens');
            $table->integer('quantity')->default(1);
            $table->float('priceSold')->default(0);
            $table->float('priceCost')->default(0);
            $table->float('discount')->default(0);
            $table->float('totalDiscount')->default(0);
            $table->float('tax')->default(0);
            $table->float('totalTax')->default(0);
            $table->float('spent')->default(0);
            $table->float('finalPrice')->default(0);
            $table->float('totalItem')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puchase_items');
    }
};
