<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();

            /* ---------- Core business identity ---------- */
            $table->string('shop_name');
            $table->string('legal_name')->nullable();
            $table->string('tagline')->nullable();

            /* ---------- Contact & location ---------- */
            $table->string('phone_primary', 30)->nullable();
            $table->string('phone_secondary', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('country')->nullable();

            /* ---------- Regulatory / accounting ---------- */
            $table->string('tax_id')->nullable();
            $table->string('vat_registration_no')->nullable();
            $table->string('currency_code', 10)->default('BDT');
            $table->string('timezone')->default('Asia/Dhaka');

            /* ---------- Branding assets ---------- */
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            /* ---------- Document footers & notes ---------- */
            $table->text('invoice_footer')->nullable();
            $table->text('email_signature')->nullable();

            /* ---------- Site footer ---------- */
            $table->text('site_footer')->nullable();

            /* ---------- Social networks ---------- */
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('x')->nullable();        // Twitter/X
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();

            /* ---------- Misc preferences ---------- */
            $table->string('default_language', 10)->default('en');
            $table->boolean('invoice_auto_print')->default(false);
            $table->enum('receipt_paper_size', ['A4', 'A5', '80mm', '58mm'])->default('A4');

            $table->timestamps();
        });

        // Seed with a starter row
        DB::table('general_settings')->insert([
            'shop_name'  => 'My Shop',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
