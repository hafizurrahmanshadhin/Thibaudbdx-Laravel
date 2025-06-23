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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->boolean('reminder')->default(false);
            $table->integer('reminder_time')->nullable();
            $table->enum('status', ['active', 'inactive', 'in_progress', 'upcoming', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
