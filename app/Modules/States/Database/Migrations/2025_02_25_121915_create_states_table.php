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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('name_in_bangla', 191)->nullable();
            $table->string('name_in_arabic', 191)->nullable();
            $table->foreignId('country_id')->references('id')->on('countries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('states');
    }
};
