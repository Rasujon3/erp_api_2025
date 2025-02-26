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
            $table->string('code', 45);
            $table->string('name', 191);
            $table->string('name_in_bangla', 191);
            $table->string('name_in_arabic', 191);
            $table->foreignId('country_id')->references('id')->on('countries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->boolean('is_default')->default(false);
            $table->boolean('draft')->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->boolean('is_active')->default(true);
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
