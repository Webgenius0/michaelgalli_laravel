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
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Name of the meal plan');
            $table->integer('people')->comment('Number of people the meal plan serves');
            $table->integer('recipes_per_week')->comment('Number of recipes included in the meal plan per week');
            $table->decimal('price_per_serving', 8, 2);
            $table->string('stripe_price_id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
