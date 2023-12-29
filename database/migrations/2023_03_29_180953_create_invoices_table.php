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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('orderNumber');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->foreign('delivery_id')->references('id')->on('deliveries');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->float('TotalInvoice')->default(0);
            $table->float('discount')->default(0);
            $table->float('TotalMerchandise')->default(0);
            $table->float('tax')->default(0);
            $table->enum('state',['Cotação','Pago','Publicado','Anulado'])->default('Cotação');
            $table->date('DateOrder')->nullable();
            $table->date('DateDue')->nullable();
            $table->float('RestPayable')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
