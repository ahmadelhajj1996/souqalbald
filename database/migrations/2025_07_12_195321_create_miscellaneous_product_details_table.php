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
        Schema::create('miscellaneous_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('group_type', ['fashion', 'beauty products', 'Sports', 'baby supplies', 'medical supplies'])->nullable();

            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('season')->nullable();
            $table->string('color')->nullable();
            $table->string('warranty')->nullable();

            $table->string('material')->nullable();
            $table->string('special_characteristics')->nullable();
            $table->string('accessories')->nullable();

            $table->string('age_group')->nullable();

            $table->string('year_of_manufacture')->nullable();
            $table->string('max_endurance')->nullable();
            $table->string('compatible_vehicles')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miscellaneous_product_details');
    }
};
