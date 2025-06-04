<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Question;
use App\Models\UserAnswer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\UserFamilyMember;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserFamilyMemberController extends Controller
{
    use ApiResponse;
    public function familyList(Request $request)
    {
        $familyMembers = $request->user()->familyMembers()->get();


        if ($familyMembers->isEmpty()) {
            return $this->error([], 'No Family Members Found', 404);
        }




        return $this->success($familyMembers, 'Family Members List Retrieved Successfully');
    }



    public function familyStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'relation' => 'required',
            'age' => 'nullable',
            'weight' => 'nullable',
            'height' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }



        $familyMember = $request->user()->familyMembers()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'relation' => $request->relation,
            'age' => $request->age,
            'weight' => $request->weight,
            'height' => $request->height,

        ]);

        return $this->success($familyMember, 'Family Member Created Successfully');
    }


    // edit
    public function familyEdit(Request $request, $id)
    {
        $familyMember = $request->user()->familyMembers()->find($id);

        if (!$familyMember) {
            return $this->error([], 'Family Member Not Found', 404);
        }

        return $this->success($familyMember, 'Family Member Retrieved Successfully');
    }

    // update
    public function familyUpdate(Request $request, $id)
    {
        $familyMember = $request->user()->familyMembers()->find($id);

        if (!$familyMember) {
            return $this->error([], 'Family Member Not Found', 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'relation' => 'required',
            'age' => 'nullable',
            'weight' => 'nullable',
            'height' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $familyMember->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'relation' => $request->relation,
            'age' => $request->age,
            'weight' => $request->weight,
            'height' => $request->height,

        ]);

        return $this->success($familyMember, 'Family Member Updated Successfully');
    }




    public function familyDelete(Request $request, $id)
    {
        $familyMember = $request->user()->familyMembers()->find($id);

        if (!$familyMember) {
            return $this->error([], 'Family Member Not Found', 404);
        }

        $familyMember->delete();

        return $this->success([], 'Family Member Deleted Successfully');
    }



    // quiz store
    public function quizStore(Request $request)
    {
        // dd($request->all()); // <-- REMOVE or COMMENT THIS LINE

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'user_family_member_id' => 'required|exists:user_family_members,id',

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
                    'user_family_member_id' => $validated['user_family_member_id'],

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

        return Helper::jsonResponse(true, 'Family Member Answers submitted successfully', 200, $submittedAnswers);
    }

    /**
     * Determine if answer should be saved as free text.
     */
    private function shouldSaveAsText($type)
    {
        return in_array($type, ['text', 'number', 'input', 'short_text']);
    }

    /**
     * Determine if answer should be saved as selected option.
     */
    private function shouldSaveAsOption($type)
    {
        return in_array($type, ['multiple_choice', 'single_choice', 'yes_no']);
    }
}
