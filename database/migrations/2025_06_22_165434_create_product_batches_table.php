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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
            // Optional: only used if product has variants
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
        
            $table->string('batch_number')->nullable(); // Optional, or auto-generated
            $table->integer('quantity')->default(0); // Quantity in this batch
        
            $table->decimal('purchase_price', 10, 2); // Cost price
            $table->decimal('sale_price', 10, 2)->nullable(); // Optional batch-based sale price
        
            $table->date('expiry_date')->nullable(); // For items with expiry
            $table->date('purchase_date')->nullable(); // When this batch was received
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
