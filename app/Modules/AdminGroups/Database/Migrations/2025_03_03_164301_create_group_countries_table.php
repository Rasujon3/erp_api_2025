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
        Schema::create('group_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_group_id')->references('id')->on('admin_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('country_id')->references('id')->on('countries')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_countries');
    }
};
