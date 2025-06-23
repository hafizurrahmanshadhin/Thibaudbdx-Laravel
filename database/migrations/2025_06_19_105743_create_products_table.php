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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('wine_name')->nullable();
            $table->string('cuvee')->nullable();
            $table->enum('type', ['Appellation', 'AOC', 'IGP']);
            $table->string('color')->nullable();
            $table->string('soil_type')->nullable();
            $table->text('harvest_ageing')->nullable();
            $table->text('food')->nullable();
            $table->text('tasting_notes')->nullable();
            $table->text('awards')->nullable();
            $table->string('image')->nullable();
            $table->string('company_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['active', 'inactive', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
