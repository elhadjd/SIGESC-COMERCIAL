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
        Schema::create('apps_translates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')->references('id')->on('apps');
            $table->string('local');
            $table->string('translate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps_translates');
    }
};
