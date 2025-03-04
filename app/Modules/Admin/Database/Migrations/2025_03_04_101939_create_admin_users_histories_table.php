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
        Schema::create('admin_users_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('users_id');
            $table->dateTime('action_date');
            $table->string('action_type');
            $table->string('export_type');
            $table->boolean('export_pdf')->default(0);
            $table->boolean('export_xls')->default(0);
            $table->boolean('export_print')->default(0);
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
        Schema::dropIfExists('admin_users_histories');
    }
};
