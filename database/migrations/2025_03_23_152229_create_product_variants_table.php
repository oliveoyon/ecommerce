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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // FK to products table
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('set null'); // FK to colors table
            $table->foreignId('size_id')->nullable()->constrained()->onDelete('set null'); // FK to sizes table
            $table->integer('quantity_in_stock')->default(0); // Stock quantity for this variant
            $table->decimal('variant_price', 10, 2)->nullable(); // Price specific to this variant (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
