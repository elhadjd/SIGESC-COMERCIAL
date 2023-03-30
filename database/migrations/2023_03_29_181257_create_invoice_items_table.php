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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->unsignedBigInteger('produtos_id');
            $table->foreign('produtos_id')->references('id')->on('produtos');
            $table->integer('quantity')->default(0);
            $table->float('PriceCost')->default(0);
            $table->float('PriceSold')->default(0);
            $table->float('TotalSold')->default(0);
            $table->float('TotalCost')->default(0);
            $table->float('Discount')->default(0);
            $table->float('TotalDiscount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
