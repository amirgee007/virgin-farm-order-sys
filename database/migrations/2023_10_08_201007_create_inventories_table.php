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

        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');

            $table->date('uploaded_date')->nullable();
            $table->date('end_valid_date')->nullable();
            $table->date('start_valid_date')->nullable();
//            $table->string('vendor' , 250)->nullable();
            $table->text('product_text')->nullable();
            $table->string('image' , 350)->nullable();
            $table->integer('unit_price')->default(0);
            $table->string('bunch' , 100)->nullable();
            $table->boolean('is_available')->default(0);
            $table->string('volume' , 270)->nullable(); #it might be l,width,height
            $table->integer('weight')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
