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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // assumes you have a users table
            $table->foreignId('user_family_member_id')->nullable()->constrained()->onDelete('cascade'); // optional, if family member is involved
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');

            // For both text and option-based answers
            $table->text('answer_text')->nullable(); // for 'text' type answers
            $table->string('selected_option_value')->nullable(); // for 'yes_no' or 'multiple_choice'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
