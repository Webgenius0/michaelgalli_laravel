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
        Schema::create('user_plan_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_plan_option_id')->constrained()->onDelete('cascade');
            $table->decimal('price_per_serving', 8, 2);
            $table->unsignedTinyInteger('people_count');
            $table->unsignedTinyInteger('recipes_per_week');
            $table->unsignedInteger('total_servings'); // = people × recipes
            $table->decimal('total_price', 10, 2);     // = total_servings × price
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_plan_carts');
    }
};
