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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('surname');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('armagen_id');
            $table->foreign('armagen_id')->references('id')->on('armagens');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('image');
            $table->enum('nivel',['user','admin'])->default('admin');
            $table->string('password');
            $table->string('password_invoice_cancel')->nullable();
            $table->enum('state',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
