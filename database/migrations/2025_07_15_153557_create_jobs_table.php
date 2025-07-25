<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->enum('job_type', ['full_time', 'part_time', 'internship', 'remotly', 'temporary_contract']);
            $table->string('governorate');
            $table->string('location');
            $table->string('job_title');
            $table->enum('type', ['job_vacancy', 'search_for_work']);

            $table->string('salary')->nullable();
            $table->string('education')->nullable();
            $table->string('experience')->nullable();
            $table->string('skills')->nullable();

            $table->text('description')->nullable();
            $table->string('work_hours')->nullable();
            $table->string('start_date')->nullable();

            $table->string('phone_number', 20);
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
