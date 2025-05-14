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
        Schema::create('recipes', function (Blueprint $table) {


            $table->id();
            $table->string('title');

            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('image_url');


            $table->integer('protein_id')->nullable();
            $table->integer('calories_id')->nullable();
            $table->integer('carb_id')->nullable();
            $table->integer('cuisine_id')->nullable();
            $table->integer('health_goal_id')->nullable();
            $table->integer('time_to_clock_id')->nullable();


            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
