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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Supplier's name
            $table->string('contact_person')->nullable(); // Contact person at the supplier
            $table->string('phone')->nullable(); // Supplier's phone number
            $table->string('email')->nullable(); // Supplier's email
            $table->string('address')->nullable(); // Supplier's address
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
