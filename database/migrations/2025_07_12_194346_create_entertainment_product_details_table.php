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
        Schema::create('entertainment_product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('group_type', ['playStation', 'musical instruments', 'books & magazines', 'video games'])->nullable();

            $table->string('type')->nullable();
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->boolean('attached_games')->nullable();
            $table->string('num_of_accessories_supplied')->nullable();
            $table->string('warranty')->nullable();
            $table->string('date_of_purchase')->nullable();

            $table->string('color')->nullable();
            $table->string('brand')->nullable();
            $table->string('accessories')->nullable();

            $table->string('title_of_book')->nullable();
            $table->string('language')->nullable();
            $table->integer('number_of_copies')->nullable();
            $table->string('author')->nullable();
            $table->string('edition')->nullable();
            $table->string('publishing_house_and_year')->nullable();

            $table->string('name')->nullable();
            $table->string('version')->nullable();
            $table->boolean('online_availability')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entertainment_product_details');
    }
};
