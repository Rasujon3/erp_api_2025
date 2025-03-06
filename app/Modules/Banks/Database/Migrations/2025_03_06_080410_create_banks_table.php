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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 191)->nullable();
            $table->string('account_number', 191)->nullable();
            $table->string('branch_name', 191)->nullable();
            $table->string('iban_number', 191)->nullable();
            $table->text('bank_details')->nullable();
            $table->string('opening_balance', 191)->nullable();
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('draft')->nullable()->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_deleted')->nullable()->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
