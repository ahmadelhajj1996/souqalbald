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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('price')->nullable();
            // $table->enum('price_type', ['free', 'negotiable', 'non-negotiable'])->nullable();
            $table->string('governorate');
            $table->string('location');
            $table->string('days_hours')->nullable();
            $table->string('phone_number', 20);
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
