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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable()->default(NULL);
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('CASCADE');
            $table->string('image')->default('produto-sem-imagem.png');
            $table->string('codego')->nullable()->default(NULL);
            $table->integer('category_product_id')->nullable();
            $table->integer('product_type_id')->nullable()->default(NULL);
            $table->string('fabricante')->nullable()->default(NULL);
            $table->float('preçocust')->default(0);
            $table->string('imposto')->nullable()->default(NULL);
            $table->float('preçovenda')->default(0);
            $table->float('preco_medio')->default(0);
            $table->integer('qtd')->nullable()->default(0);
            $table->float('total_cust')->default(0);
            $table->enum('inventory',['true','false'])->default('false');
            $table->boolean('shop_online')->default('false');
            $table->timestamp('datafab')->nullable()->default(NULL);
            $table->timestamp('dataexp')->nullable()->default(NULL);
            $table->enum('estado',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
