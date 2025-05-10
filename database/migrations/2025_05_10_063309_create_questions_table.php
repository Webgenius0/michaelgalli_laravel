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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['text', 'yes_no', 'multiple_choice'])->default('text');
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);

            // For conditional logic
            $table->foreignId('conditional_to_question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->string('conditional_answer_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
