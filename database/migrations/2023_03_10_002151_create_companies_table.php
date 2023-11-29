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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('shopOnline')->default(false);
            $table->string('nif')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('city')->nullable();
            $table->string('sede')->nullable();
            $table->string('house_number')->nullable();
            $table->string('country')->nullable();
            $table->string('longitude')->default(null);
            $table->string('latitude')->default(null);
            $table->unsignedBigInteger('manager')->nullable();
            $table->unsignedBigInteger('activity_type_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
