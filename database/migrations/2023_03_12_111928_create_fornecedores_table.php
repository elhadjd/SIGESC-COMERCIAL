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
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);
            $table->string('image')->default('produto-sem-imagem.png');
            $table->integer('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('CASCADE');
            $table->string('nif')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('country')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('sede')->nullable()->default(null);
            $table->enum('state',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedores');
    }
};
