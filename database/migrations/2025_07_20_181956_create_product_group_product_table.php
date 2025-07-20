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
        // pivot
        Schema::create('product_group_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_group_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('stems')->default(0);

            $table->foreign('product_group_id')->references('id')->on('product_groups')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_group_product');
    }
};
