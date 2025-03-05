<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('name_in_bangla', 191)->nullable();
            $table->string('name_in_arabic', 191)->nullable();
            $table->boolean('is_default')->nullable()->default(false);
            $table->boolean('draft')->nullable()->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->string('flag')->nullable(); // Add symbols field
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
