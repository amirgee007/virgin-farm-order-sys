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
        Schema::create('client_shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name' , 350)->nullable();
            $table->string('company' , 350)->nullable();
            $table->string('phone' , 100)->nullable();
            $table->string('address' , 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_shipping_addresses');
    }
};
