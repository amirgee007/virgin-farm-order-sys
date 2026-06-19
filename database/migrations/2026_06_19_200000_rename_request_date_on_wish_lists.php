<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wish_lists', function (Blueprint $table) {
            $table->renameColumn('request_date', 'ship_date');
        });
    }

    public function down(): void
    {
        Schema::table('wish_lists', function (Blueprint $table) {
            $table->renameColumn('ship_date', 'request_date');
        });
    }
};
