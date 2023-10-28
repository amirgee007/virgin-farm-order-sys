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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->text('product_text')->nullable();

            $table->string('vendor' , 50)->nullable();
            $table->boolean('is_deal')->default(0);
            $table->float('unit_price' , 8,2)->default(0);
            $table->smallInteger('stems')->default(0);
            $table->smallInteger('quantity')->default(0);
            $table->string('box_type' , 50)->nullable();
            $table->string('units_box' , 100)->nullable();

            $table->string('category' , 100)->nullable();
            $table->string('color' , 100)->nullable();

            $table->string('image_url' , 250)->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
