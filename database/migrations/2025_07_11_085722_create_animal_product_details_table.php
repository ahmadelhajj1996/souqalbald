<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('animal_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('group_type', ['animal', 'veterinary', 'supply'])->nullable();

            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('age')->nullable();
            $table->enum('gender', ['male', 'female', 'ذكر', 'أنثى'])->nullable();

            $table->string('service_type')->nullable();
            $table->string('specialization')->nullable();
            $table->string('service_provider_name')->nullable();
            $table->string('work_time')->nullable();

            $table->string('services_price')->nullable();

            $table->string('vaccinations')->nullable();

            $table->string('model_or_size')->nullable();
            $table->string('color')->nullable();
            $table->string('appropriate_to')->nullable();

            $table->string('accessories')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('animal_product_details');
    }
};
