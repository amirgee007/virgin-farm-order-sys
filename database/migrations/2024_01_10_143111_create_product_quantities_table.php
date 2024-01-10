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
        Schema::create('product_quantities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->string('item_no' , 30)->nullable();
            $table->smallInteger('quantity')->default(0);
            $table->float('price_fob' , 8, 2)->default(0);
            $table->float('price_fedex' , 8, 2)->default(0);
            $table->float('price_hawaii' , 8, 2)->default(0);

            $table->date('date_in')->nullable();
            $table->date('date_out')->nullable();
            $table->timestamps();
        });

        Schema::table('product_quantities', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('product_id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_quantities');
    }
};
