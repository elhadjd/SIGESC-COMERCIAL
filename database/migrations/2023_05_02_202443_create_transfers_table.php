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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('orderNumber');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('store_principal_id')->nullable();
            $table->foreign('store_principal_id')->references('id')->on('armagens');
            $table->unsignedBigInteger('store_destination_id')->nullable();
            $table->foreign('store_destination_id')->references('id')->on('armagens');
            $table->float('total_spent')->default(0)->nullable();
            $table->enum('state',['Cotação','Publicado','Anulado'])->default('Cotação');
            $table->float('total')->default(0);
            $table->date('DateOrder')->default(now());
            $table->date('DateDue')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
