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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('price_type', ['free', 'negotiable', 'non-negotiable', 'مجاني', 'قابل للتفاوض', 'غير قابل للتفاوض'])->nullable();
            $table->enum('state', ['new', 'used', 'مستعمل', 'جديد'])->nullable();
            $table->enum('governorate', [
                'دمشق',
                'ريف دمشق',
                'حلب',
                'حمص',
                'حماة',
                'اللاذقية',
                'طرطوس',
                'إدلب',
                'درعا',
                'السويداء',
                'دير الزور',
                'الرقة',
                'الحسكة',
                'القنيطرة',
            ]);
            $table->string('address_details')->nullable();
            $table->text('description')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
