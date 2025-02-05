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
            $table->string('code', 45);
            $table->string('name', 191);
            $table->boolean('is_default')->default(false);
            $table->boolean('draft')->default(false);
            $table->timestamp('drafted_at')->nullable();
            $table->boolean('is_active')->default(true);
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
