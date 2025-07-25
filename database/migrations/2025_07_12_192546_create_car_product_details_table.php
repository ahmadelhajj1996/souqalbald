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
        Schema::create('car_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('group_type', ['cars', 'motorcycles', 'bicycles', 'tires & supplies'])->nullable();
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('kilometers')->nullable();
            $table->string('fuel_type')->nullable();
            $table->enum('dipstick', ['normal', 'automatic', 'half_automatic', 'عادي', 'أوتوماتيك', 'نصف أوتوماتيك'])->nullable();
            $table->string('engine_capacity')->nullable();
            $table->string('num_of_doors')->nullable();
            $table->string('topology_status')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_product_details');
    }
};
