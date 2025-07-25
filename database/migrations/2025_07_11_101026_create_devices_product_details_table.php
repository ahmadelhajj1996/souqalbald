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
        Schema::create('devices_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->enum('device_type', ['mobile', 'laptop', 'tv'])->nullable();

            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->text('made_in')->nullable();
            $table->date('year_of_manufacture')->nullable();
            $table->string('screen_size')->nullable();
            $table->string('warranty')->nullable();
            $table->string('accessories')->nullable();
            $table->string('camera')->nullable();
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->boolean('supports_sim')->nullable();
            $table->string('operation_system')->nullable();
            $table->string('screen_card')->nullable();
            $table->string('ram')->nullable();
            $table->string('processor')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_product_details');
    }
};
