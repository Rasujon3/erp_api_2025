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
        Schema::create('admin_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 6);
            $table->string('name', 40);
            $table->unsignedBigInteger('client_id');
            $table->enum('type', ['system', 'super_admin', 'admin', 'super_user', 'user']); // Add appropriate ENUM values
            $table->string('password');
            $table->string('confirm_password');
            $table->boolean('otp_enable')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_draft')->default(0);
            $table->boolean('is_delete')->default(0);
            $table->string('address', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 40)->nullable();
            $table->string('website', 40)->nullable();
            $table->string('location', 255)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('client_id')
                ->references('id')
                ->on('admin_clients')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_clients');
    }
};
