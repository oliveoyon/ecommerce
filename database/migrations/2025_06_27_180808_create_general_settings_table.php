<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();

            /* ---------- Core business identity ---------- */
            $table->string('shop_name');
            $table->string('legal_name')->nullable();          // e.g. registered company name
            $table->string('tagline')->nullable();             // short slogan for invoices / site

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
            $table->string('tax_id')->nullable();              // BIN / VAT / TIN
            $table->string('vat_registration_no')->nullable();
            $table->string('currency_code', 10)->default('BDT');
            $table->string('timezone')->default('Asia/Dhaka');

            /* ---------- Branding assets ---------- */
            $table->string('logo_path')->nullable();           // stored in /storage/app/public
            $table->string('favicon_path')->nullable();

            /* ---------- Document footers & notes ---------- */
            $table->text('invoice_footer')->nullable();        // e.g. “Goods once sold…”
            $table->text('email_signature')->nullable();

            /* ---------- Misc preferences ---------- */
            $table->string('default_language', 10)->default('en');
            $table->boolean('invoice_auto_print')->default(false);
            $table->enum('receipt_paper_size', ['A4','A5','80mm','58mm'])->default('A4');

            $table->timestamps();
        });

        // Populate an initial empty row so you never get “null” lookups.
        DB::table('general_settings')->insert([
            'shop_name'   => 'My Shop',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
