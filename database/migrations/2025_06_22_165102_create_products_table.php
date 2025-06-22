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
            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignId('brand_id')->constrained()->onDelete('set null')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('set null')->nullable();

            $table->boolean('has_variants')->default(false); // true = use product_variants
            $table->boolean('has_expiry')->default(false); // useful for medicine, food, etc.

            $table->decimal('default_purchase_price', 10, 2)->nullable(); // only used if no variant or batch
            $table->decimal('default_sale_price', 10, 2)->nullable();
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
