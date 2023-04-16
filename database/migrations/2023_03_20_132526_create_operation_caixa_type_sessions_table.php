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
        Schema::create('operation_caixa_type_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->integer('operation_caixa_type_id')->nullable();
            $table->foreign('operation_caixa_type_id')->references('id')->on('operation_caixa_types');
            $table->text('subject');
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_caixa_type_sessions');
    }
};
