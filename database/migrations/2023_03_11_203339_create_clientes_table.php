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
            $table->string('surname')->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('whatsapp')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('country')->nullable()->default(null);
            $table->string('rua')->nullable()->default(null);
            $table->integer('state')->nullable()->default(null);
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
