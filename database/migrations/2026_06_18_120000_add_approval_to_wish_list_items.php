<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wish_list_items', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('quantity');
            $table->decimal('quoted_price', 10, 2)->nullable()->after('approval_status');
            $table->string('admin_note', 500)->nullable()->after('quoted_price');
        });
    }

    public function down(): void
    {
        Schema::table('wish_list_items', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'quoted_price', 'admin_note']);
        });
    }
};
