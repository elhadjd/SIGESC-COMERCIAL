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
        Schema::create('puchases', function (Blueprint $table) {
            $table->id();
            $table->string('orderNumber')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('fornecedor_id')->nullable();
            $table->foreign('fornecedor_id')->references('id')->on('fornecedores');
            $table->unsignedBigInteger('armagen_id')->nullable();
            $table->foreign('armagen_id')->references('id')->on('armagens');
            $table->float('TotalSpending')->default(0);
            $table->float('totalMerchandise')->default(0);
            $table->float('totalDiscount')->default(0);
            $table->float('totalTax')->default(0);
            $table->float('total')->default(0);
            $table->float('restPayable')->default(0);
            $table->enum('state',['Publicado','Cotação','Anulado','Pago'])->default('Cotação');
            $table->date('DateOrder')->nullable();
            $table->date('DateDue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puchases');
    }
};
