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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('company_name', 191)->nullable();
            $table->string('website', 60)->nullable();
            $table->string('vat_number', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('state', 191)->nullable();
            $table->foreignId('bank_id')->references('id')->on('banks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('country_id')->references('id')->on('countries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('currency_id')->references('id')->on('currencies')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('zip_code', 60)->nullable();
            $table->string('phone', 60)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('draft')->nullable()->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_deleted')->nullable()->default(false);
            $table->boolean('status')->nullable()->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
