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
        Schema::create('admin_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 191)->nullable();
            $table->string('english', 191)->nullable();
            $table->string('arabic', 191)->nullable();
            $table->string('bengali', 191)->nullable();
            $table->string('group_name', 191)->nullable();
//            $table->foreignId('country_id')->references('id')->on('countries')
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('is_draft')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);
            $table->boolean('is_deleted')->nullable()->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->string('flag')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_groups');
    }
};
