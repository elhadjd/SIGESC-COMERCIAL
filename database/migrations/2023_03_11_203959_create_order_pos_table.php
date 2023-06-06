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
        Schema::create('order_pos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->float('total')->nullable();
            $table->float('total_costs')->nullable();
            $table->string('cliente')->nullable();
            $table->enum('state',['Pago','Anulado','Pendente'])->default('Pago');
            $table->integer('number')->nullable();
            $table->integer('print')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_pos');
    }
};
