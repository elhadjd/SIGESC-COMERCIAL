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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('image')->default('produto-sem-imagem.png');
            $table->string('surname')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('phone')->nullable();
            $table->text('token')->nullable();
            $table->text('user_id_clerk')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('rua')->nullable();
            $table->integer('state')->nullable();
            $table->string('currency')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
