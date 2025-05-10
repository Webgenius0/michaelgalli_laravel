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

        // dd($request->all());
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'nullable|string',
            'answers.*.selected_option_value' => 'nullable|string',
        ]);

        $submittedAnswers = [];

        foreach ($validated['answers'] as $answerData) {
            $question = Question::with('options')->find($answerData['question_id']);

            $userAnswer = UserAnswer::updateOrCreate(
                [
                    'user_id' => auth('api')->user()->id,
                    'quiz_id' => 1,
                    'question_id' => $question->id,
                ],
                [
                    'answer_text' => in_array($question->question_type, ['single_choice', 'multi_choice']) || is_numeric($answerData['answer'] ?? null)
                        ? $answerData['answer']
                        : null,

                    'selected_option_value' => in_array($question->question_type, ['yes_no', 'multiple_choice'])
                        ? $answerData['selected_option_value']
                        : null,
                ]
            );

            $submittedAnswers[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'answer' => $userAnswer->answer_text ?? $userAnswer->selected_option_value,
            ];
        }

        return Helper::jsonResponse(true, 'Answers submitted successfully', 200, $submittedAnswers);
    }
}
