<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wish_list_items', function (Blueprint $table) {
            $table->enum('customer_decision', ['pending', 'accepted', 'rejected'])
                ->default('pending')
                ->after('admin_note');
            $table->timestamp('customer_decided_at')->nullable()->after('customer_decision');
        });

        DB::statement("ALTER TABLE wish_lists MODIFY status ENUM('draft','submitted','quoted','confirmed','closed') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('wish_list_items', function (Blueprint $table) {
            $table->dropColumn(['customer_decision', 'customer_decided_at']);
        });

        DB::statement("ALTER TABLE wish_lists MODIFY status ENUM('draft','submitted','quoted','closed') NOT NULL DEFAULT 'draft'");
    }
};
