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
        Schema::create('country_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->references('id')->on('admin_clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamp('action_date')->nullable();
            $table->string('action_by', 60)->nullable();
            $table->string('action_type', 60)->nullable();
            $table->string('export_type', 60)->nullable();
            $table->boolean('export_pdf')->nullable()->default(false);
            $table->boolean('export_xls')->nullable()->default(false);
            $table->boolean('export_print')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_histories');
    }
};
