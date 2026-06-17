<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wish_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('sales_rep')->nullable();
            $table->date('request_date')->nullable();
            $table->enum('status', ['draft', 'submitted', 'quoted', 'closed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('sales_rep');
            $table->index('status');
        });

        Schema::create('wish_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wish_list_id');
            $table->integer('product_id');
            $table->string('item_no');
            $table->string('name')->nullable();
            $table->integer('quantity');
            $table->string('image', 350)->nullable();
            $table->string('size')->nullable();
            $table->string('stems')->nullable();
            $table->timestamps();

            $table->index('wish_list_id');
            $table->unique(['wish_list_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wish_list_items');
        Schema::dropIfExists('wish_lists');
    }
};
