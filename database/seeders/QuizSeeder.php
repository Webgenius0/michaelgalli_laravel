<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = Quiz::create([
            'title' => 'Personal Nutrition Quiz',
            'description' => 'Answer the following to tailor your food preferences.',
            'status' => 'active',
        ]);

        // Q1: Dairy Response (multiple choice)
        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '1. How does your body respond to dairy (milk, cheese, yogurt)?',
            'question_type' => 'multiple_choice',
            'is_required' => true,
            'order' => 1,
        ]);
        QuestionOption::insert([
            ['question_id' => $q1->id, 'option_text' => 'I avoid it – I\'m lactose intolerant.', 'option_value' => 'lactose_intolerant'],
            ['question_id' => $q1->id, 'option_text' => 'I sometimes feel bloated.', 'option_value' => 'bloated'],
            ['question_id' => $q1->id, 'option_text' => 'I\'m fine with dairy.', 'option_value' => 'fine_with_dairy'],
            ['question_id' => $q1->id, 'option_text' => 'Other.', 'option_value' => 'other'],
        ]);

        // Q2: Gluten issues (yes/no)
        $q2 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '2. Do you have any known issues with gluten or wheat products?',
            'question_type' => 'yes_no',
            'is_required' => true,
            'order' => 2,
        ]);
        QuestionOption::insert([
            ['question_id' => $q2->id, 'option_text' => 'Yes', 'option_value' => 'yes'],
            ['question_id' => $q2->id, 'option_text' => 'Occasionally', 'option_value' => 'occasionally'],
            ['question_id' => $q2->id, 'option_text' => 'No', 'option_value' => 'no'],
        ]);

        // Q3: Carb-heavy meals (multiple choice)
        $q3 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '3. How do you usually feel after eating carb-heavy meals (rice, pasta, bread)?',
            'question_type' => 'multiple_choice',
            'is_required' => true,
            'order' => 3,
        ]);
        QuestionOption::insert([
            ['question_id' => $q3->id, 'option_text' => 'Energized and satisfied', 'option_value' => 'energized'],
            ['question_id' => $q3->id, 'option_text' => 'I crash or feel sleepy afterward', 'option_value' => 'crash'],
            ['question_id' => $q3->id, 'option_text' => 'I feel bloated or gassy', 'option_value' => 'bloated'],
            ['question_id' => $q3->id, 'option_text' => 'Other.', 'option_value' => 'other'],
        ]);

        // Q4: Low in vitamins (yes/no)
        $q4 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '4. Have you ever been told you’re low in vitamins like B12, D3, or Iron?',
            'question_type' => 'yes_no',
            'is_required' => true,
            'order' => 4,
        ]);
        QuestionOption::insert([
            ['question_id' => $q4->id, 'option_text' => 'Yes', 'option_value' => 'yes'],
            ['question_id' => $q4->id, 'option_text' => 'Not sure', 'option_value' => 'not_sure'],
            ['question_id' => $q4->id, 'option_text' => 'No', 'option_value' => 'no'],
            ['question_id' => $q1->id, 'option_text' => 'Other.', 'option_value' => 'other'],

        ]);

        // Q4 follow-up: If yes, which vitamins?
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '4. If yes, which vitamins were low (like B12, D3, iron)?',
            'question_type' => 'text',
            'is_required' => false,
            'order' => 5,
            'conditional_to_question_id' => $q4->id,
            'conditional_answer_value' => 'yes',
        ]);

        // Q5: Preferred diet
        $q5 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '5. What type of diet do you prefer?',
            'question_type' => 'multiple_choice',
            'is_required' => true,
            'order' => 6,
        ]);
        QuestionOption::insert([
            ['question_id' => $q5->id, 'option_text' => 'Vegan', 'option_value' => 'vegan'],
            ['question_id' => $q5->id, 'option_text' => 'Vegetarian', 'option_value' => 'vegetarian'],
            ['question_id' => $q5->id, 'option_text' => 'Non-vegetarian', 'option_value' => 'non_vegetarian'],
        ]);

        // Q6: Ingredients disliked/avoided
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '6. Are there any ingredients you dislike or avoid?',
            'question_type' => 'text',
            'is_required' => false,
            'order' => 7,
        ]);

        // Q7: Time to cook dinner
        $q7 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '7. How much time do you usually have to cook dinner?',
            'question_type' => 'multiple_choice',
            'is_required' => true,
            'order' => 8,
        ]);
        QuestionOption::insert([
            ['question_id' => $q7->id, 'option_text' => '< 15 minutes', 'option_value' => 'less_15'],
            ['question_id' => $q7->id, 'option_text' => '15 – 30 minutes', 'option_value' => '15_30'],
            ['question_id' => $q7->id, 'option_text' => '30 – 60 minutes', 'option_value' => '30_60'],
            ['question_id' => $q7->id, 'option_text' => '> 1 hour', 'option_value' => 'more_60'],
        ]);

        // Q8: Favorite cuisines
        $q8 = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '8. Which cuisines do you enjoy the most?',
            'question_type' => 'multiple_choice',
            'is_required' => false,
            'order' => 9,
        ]);
        QuestionOption::insert([
            ['question_id' => $q8->id, 'option_text' => 'Italian', 'option_value' => 'italian'],
            ['question_id' => $q8->id, 'option_text' => 'Indian', 'option_value' => 'indian'],
            ['question_id' => $q8->id, 'option_text' => 'Asian', 'option_value' => 'asian'],
            ['question_id' => $q8->id, 'option_text' => 'Mexican', 'option_value' => 'mexican'],
        ]);

        // Q9: Meal delivery or kits
        Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => '9. Would you like meals with food delivery or DIY kits?',
            'question_type' => 'multiple_choice',
            'is_required' => true,
            'order' => 10,
        ]);
        QuestionOption::insert([
            ['question_id' => $quiz->id + 9, 'option_text' => 'Food delivery', 'option_value' => 'delivery'],
            ['question_id' => $quiz->id + 9, 'option_text' => 'DIY kits', 'option_value' => 'diy_kits'],
            ['question_id' => $quiz->id + 9, 'option_text' => 'Both', 'option_value' => 'both'],
        ]);
    }
}
