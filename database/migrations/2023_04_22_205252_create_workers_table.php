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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('workers_department_id')->nullable();
            $table->foreign('workers_department_id')->references('id')->on('workers_departments');
            $table->unsignedBigInteger('manager')->nullable();
            $table->foreign('manager')->references('id')->on('users');
            $table->unsignedBigInteger('trainer')->nullable();
            $table->foreign('trainer')->references('id')->on('users');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name')->nullable();
            $table->string('image')->default("produto-sem-imagem.png");
            $table->string('cargo')->nullable();
            $table->string('email')->uniqid()->nullable();
            $table->string('phone')->nullable();
            $table->string('celular')->nullable();
            $table->float('salary')->nullable();
            $table->float('dailyExpense')->nullable();
            $table->string('hourWork')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
