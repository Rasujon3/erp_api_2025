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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45);
            $table->string('name', 191);
            $table->string('name_in_bangla', 191)->nullable();
            $table->string('name_in_arabic', 191)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('draft')->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('symbol', 255)->nullable();
            $table->decimal('exchange', 8, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
