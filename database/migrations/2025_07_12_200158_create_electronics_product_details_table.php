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
        Schema::create('electronics_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('group_type', ['miscellaneous', 'furniture'])->nullable();

            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('year_of_manufacture')->nullable();
            $table->string('size_or_weight')->nullable();
            $table->string('color')->nullable();
            $table->string('warranty')->nullable();
            $table->string('accessories')->nullable();

            $table->text('main_specification')->nullable();

            $table->string('dimensions')->nullable();
            $table->text('state_specification')->nullable();
            $table->string('made_from')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronics_product_details');
    }
};
