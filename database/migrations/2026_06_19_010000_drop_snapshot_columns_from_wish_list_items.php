<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wish_list_items', function (Blueprint $table) {
            $table->dropColumn(['image', 'size', 'stems']);
        });
    }

    public function down(): void
    {
        Schema::table('wish_list_items', function (Blueprint $table) {
            $table->string('image', 350)->nullable();
            $table->string('size')->nullable();
            $table->string('stems')->nullable();
        });
    }
};
