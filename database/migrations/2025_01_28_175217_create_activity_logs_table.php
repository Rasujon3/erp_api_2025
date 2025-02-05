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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // ID of the user performing the action
            $table->string('action'); // Description of the action performed
            $table->string('module')->nullable(); // The module or feature where the action occurred
            $table->string('model')->nullable(); // The model or feature where the action occurred
            $table->bigInteger('row_id')->nullable(); // The model or feature where the action occurred
            $table->json('data')->nullable(); // Any additional data related to the action
            $table->ipAddress('ip_address')->nullable(); // IP address of the user
            $table->timestamp('created_at')->useCurrent(); // When the activity was performed

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
