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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('city')->default(null);
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clientes');
            $table->string('neighborhood')->default(null);
            $table->text('localisation');
            $table->string('road')->default(null);
            $table->string('housNumber')->default(null);
            $table->string('county')->default(null);
            $table->text('comment')->default(null);
            $table->boolean('state')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
