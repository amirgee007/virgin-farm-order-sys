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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('item_no')->nullable();
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image' , 350);
            $table->string('size');
            $table->string('stems');
            $table->integer('max_qty');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
