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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable(false);
            $table->enum('billing_interval', ['month'])->nullable(false);
            $table->decimal('price', 10, 2)->nullable(false);
            $table->string('currency')->default('USD');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
