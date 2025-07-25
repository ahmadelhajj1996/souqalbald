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
        Schema::create('real_estate_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('group_type', ['propertys', 'offices', 'lands'])->nullable();

            $table->string('type')->nullable();
            $table->string('ownership')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('num_of_room')->nullable();
            $table->string('num_of_bathroom')->nullable();
            $table->string('num_of_balconies')->nullable();
            $table->string('area')->nullable();
            $table->string('floor')->nullable();
            $table->boolean('furnished')->nullable();
            $table->text('age_of_construction')->nullable();
            $table->string('readiness')->nullable();

            $table->string('facade')->nullable();
            $table->string('nature_of_land')->nullable();
            $table->string('street_width')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_product_details');
    }
};
