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
        Schema::create('meal_plan_options', function (Blueprint $table) {

            $table->id();
            $table->foreignId('meal_plan_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('recipes_per_week'); // 3, 4, 5, etc.
            $table->decimal('price_per_serving', 8, 2);      // e.g., 30.00
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_options');
    }
};
