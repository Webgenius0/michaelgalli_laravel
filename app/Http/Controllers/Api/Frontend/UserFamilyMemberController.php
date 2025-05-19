<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\User;
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
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'nullable|string',
            'answers.*.selected_option_value' => 'nullable|string',
            'user_family_member_id' => 'required|exists:user_family_members,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $validated = $validator->validated();

        $user_family = UserFamilyMember::find($validated['user_family_member_id']);
        if (!$user_family) {
            return $this->error([], 'Family Member Not Found', 404);
        }

        $submittedAnswers = [];

        foreach ($validated['answers'] as $answerData) {
            $question = Question::with('options')->find($answerData['question_id']);

            $userAnswer = UserAnswer::updateOrCreate(
                [
                    'user_id' => auth('api')->id(),
                    'user_family_member_id' => $validated['user_family_member_id'],
                    'quiz_id' => $question->quiz_id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_text' => $this->shouldSaveAsText($question->question_type)
                        ? $answerData['answer']
                        : null,

                    'selected_option_value' => $this->shouldSaveAsOption($question->question_type)
                        ? $answerData['selected_option_value']
                        : null,
                ]
            );

            $submittedAnswers[] = [
                'user_family_member_id' => $validated['user_family_member_id'],
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'answer' => $userAnswer->answer_text ?? $userAnswer->selected_option_value,
            ];
        }

        return $this->success($submittedAnswers, 'Family Quiz Answers Submitted Successfully');
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
