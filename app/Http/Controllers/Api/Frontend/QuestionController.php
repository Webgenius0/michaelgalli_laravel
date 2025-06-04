<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index()
    {

        $question_lists = Question::with('options')->get();


        $formatted = $question_lists->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'is_required' => $question->is_required,
                'order' => $question->order,
                'options' => $question->options->map(function ($opt) {
                    return [
                        'option_text' => $opt->option_text,
                        'option_value' => $opt->option_value,
                    ];
                }),
            ];
        });

        return Helper::jsonResponse(true, 'Question with option retrived  successfully', 200, $formatted);
    }




    // store user answer 

    public function store(Request $request)
    {
        // dd($request->all()); // <-- REMOVE or COMMENT THIS LINE

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'nullable|string',
            'answers.*.option_value' => 'nullable', 
        ]);

        $submittedAnswers = [];

        foreach ($validated['answers'] as $answerData) {
            $question = Question::with('options')->find($answerData['question_id']);
            // dd($question);

            $answerText = null;
            $optionValue = null;

            if ($question->question_type === 'text') {
                $answerText = $answerData['answer'] ?? null;
                // dd($answerData);
            } elseif ($question->question_type === 'yes_no') {
                $optionValue = $answerData['option_value'] ?? null;
            } elseif ($question->question_type === 'multiple_choice') {
                // Accept both single and multiple selections
                $optionValue = isset($answerData['option_value'])
                    ? (is_array($answerData['option_value']) ? json_encode($answerData['option_value']) : $answerData['option_value'])
                    : null;
            }

            $userAnswer = UserAnswer::updateOrCreate(
                [
                    'user_id' => auth('api')->user()->id,
                    'quiz_id' => $question->quiz_id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_text' => $answerText,
                    'selected_option_value' => $optionValue,
                ]
            );

            $submittedAnswers[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'answer' => $userAnswer->answer_text ?? $userAnswer->option_value,
            ];
        }

        return Helper::jsonResponse(true, 'Answers submitted successfully', 200, $submittedAnswers);
    }
}
