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
        Schema::create('enrollments', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('price_bought');
            $table->enum('status', ['pending', 'success'])->default('success');
            $table->string('external_id', 255)->nullable();
            $table->integer('progress')->default(0);
            $table->text('payment_url')->nullable(); // 🟢 Sudah aman menggunakan TEXT & nullable sesuai phpMyAdmin cPanel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
