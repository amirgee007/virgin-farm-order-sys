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

        Schema::create('boxes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('description' , 500)->nullable();
            $table->float('length' , 7,2)->default(0);
            $table->float('width' , 7,2)->default(0);
            $table->float('height' , 7,2)->default(0);
//            $table->float('volume' , 7,2)->default(0);
            $table->float('weight' , 7,2)->default(0);

            $table->smallInteger('min_value')->default(0);

            $table->timestamps();
        });

        Schema::create('unit_of_measures', function (Blueprint $table) {
            $table->increments('id');

            $table->string('unit' , 10);
            $table->string('detail' , 250)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
        Schema::dropIfExists('unit_of_measures');
    }
};
