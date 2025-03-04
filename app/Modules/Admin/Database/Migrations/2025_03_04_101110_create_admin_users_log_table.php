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
        Schema::create('admin_users_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('users_id');
            $table->string('otp')->nullable();
            $table->dateTime('log_in_time')->nullable();
            $table->dateTime('log_out_time')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('users_id')
                ->references('id')
                ->on('admin_users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users_log');
    }
};
