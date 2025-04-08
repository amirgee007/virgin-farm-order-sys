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
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id');
            $table->date('shipping_date')->nullable();
            $table->unsignedInteger('carrier_id')->nullable();
            $table->unsignedInteger('shipping_address_id')->nullable();;
            $table->integer('sub_total')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('tarrif_tax')->default(0);
            $table->integer('shipping_cost')->default(0);
            $table->integer('total')->default(0);
//            $table->unsignedInteger('product_id')->nullable();

            $table->timestamps();
        });

        Schema::table('pre_orders', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

//        Schema::table('products', function (Blueprint $table) {
//            $table->foreign('product_id')
//                ->references('id')
//                ->on('products')
//                ->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_orders');
    }
};
