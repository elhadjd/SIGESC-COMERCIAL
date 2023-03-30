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
        Schema::create('fornecedore_produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produtos_id')->uniqid;
            $table->foreign('produtos_id')->references('id')->on('produtos')->onDelete('cascade');
            $table->unsignedBigInteger('fornecedore_id')->uniqid;
            $table->foreign('fornecedore_id')->references('id')->on('fornecedores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedore_produtos');
    }
};
