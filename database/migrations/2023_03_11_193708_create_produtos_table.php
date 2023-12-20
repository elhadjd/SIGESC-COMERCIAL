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
            $table->string('nome')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('CASCADE');
            $table->string('image')->default('produto-sem-imagem.png');
            $table->string('codego')->nullable();
            $table->integer('category_product_id')->nullable();
            $table->integer('product_type_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories');
            $table->string('fabricante')->nullable();
            $table->float('preçocust')->default(0);
            $table->string('imposto')->nullable();
            $table->float('preçovenda')->default(0);
            $table->float('preco_medio')->default(0);
            $table->integer('qtd')->nullable()->default(0);
            $table->float('total_cust')->default(0);
            $table->boolean('inventory')->default(false);
            $table->boolean('shop_online')->default(false);
            $table->timestamp('datafab')->nullable();
            $table->timestamp('dataexp')->nullable();
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
