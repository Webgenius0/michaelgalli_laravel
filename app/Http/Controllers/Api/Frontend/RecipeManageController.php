<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecipeManageController extends Controller
{
    public function recipe_list(Request $request)
    {
        // dd($request->all());
        $perPage = $request->input('per_page', 10);

        $recipes = Recipe::with([
            'category',
            'protein',
            'calory',
            'carb',
            'cuisine',
            'time_to_clock',
            'health_goal',
            'ingredientSections.ingredients',
             'instructions'
        ]);

        $recipes->when($request->filled('category_id'), function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        });

        $recipes->when($request->filled('protein_id'), function ($query) use ($request) {
            $query->where('protein_id', $request->protein_id);
        });

        $recipes->when($request->filled('calory_id'), function ($query) use ($request) {
            $query->where('calories_id', $request->calory_id);
        });

        $recipes->when($request->filled('carb_id'), function ($query) use ($request) {
            $query->where('carb_id', $request->carb_id);
        });

        $recipes->when($request->filled('cuisine_id'), function ($query) use ($request) {
            $query->where('cuisine_id', $request->cuisine_id);
        });

        $recipes->when($request->filled('health_goal_id'), function ($query) use ($request) {
            $query->where('health_goal_id', $request->health_goal_id);
        });

        $recipes->when($request->filled('time_to_clock_id'), function ($query) use ($request) {
            $query->where('time_to_clock_id', $request->time_to_clock_id);
        });

        $recipes->where(function ($query) use ($request) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('calory', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('protein', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('carb', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('health_goal', function ($q2) use ($search) {
                            $q2->where('name', 'LIKE', '%' . $search . '%');
                        });
                });
            }
        });

        $paginated = $recipes->orderBy('created_at', 'desc')->paginate($perPage);

        $data = [
            'data' => collect($paginated->items())->map(function ($item) {
                $item->image_url = $item->image_url
                ? asset( $item->image_url)
                : null;

                return $item;
            }),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ];

        return Helper::jsonResponse(true, 'Recipe List Retrieved Successfully', 200, $data);
    }

    public function recipe_details(Request $request, $id)
    {

        $recipe = Recipe::with(['ingredientSections.ingredients', 'instructions'])->find($id);
        if (! $recipe) {
            return response()->json(['success' => false, 'message' => 'Recipe not found'], 404);
        }

        // Original ingredient sections for fallback
        $originalIngredientSections = $recipe->ingredientSections->map(function ($section) {
            return [
                'section_name' => $section->title,
                'ingredients'  => $section->ingredients->map(fn($ing) => ['id' => $ing->id, 'name' => $ing->name, 'amount' => $ing->amount]),
            ];
        });

        $memberIds = $request->query('member_ids', []);

        $aiGeneratedIngredients = null;

        if (! empty($memberIds)) {
            $members = auth('api')->user()->familyMembers()->whereIn('id', $memberIds)->get();
            // dd($members);

            if ($members->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No valid family members found.'], 404);
            }

            $aiGeneratedIngredients = $this->generateAiPersonalizedIngredients($recipe, $members);
        }

        return response()->json([
            'success'               => true,
            'id'                    => $recipe->id,
            'title'                 => $recipe->title,
            'short_description'     => $recipe->short_description,
            'long_description'      => $recipe->long_description,
            'image_url'             => url($recipe->image_url),
            'original_ingredients'  => $originalIngredientSections,
            'generated_ingredients' => $aiGeneratedIngredients,
            'instructions'          => $recipe->instructions,
        ]);
    }

    protected function generateAiPersonalizedIngredients($recipe, $members)
    {
        $originalIngredients = $recipe->ingredientSections->flatMap(fn($section) => $section->ingredients->pluck('name'))->toArray();

        $memberProfiles = $members->map(function ($member) {
            return [
                'member_id'   => $member->id,
                'member_name' => $member->first_name ?? null,
                'answers'     => $member->userAnswers->map(function ($answer) {
                    return [
                        'question_id'           => $answer->question_id,
                        'answer_text'           => $answer->answer_text,
                        'selected_option_value' => $answer->selected_option_value,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // $memberProfiles = $members->map(function ($member) {
        //     $dnaTraits = $member->dnaTraits ?? collect();

        //     // If DNA traits exist, prioritize them
        //     if ($dnaTraits->isNotEmpty()) {
        //         return [
        //             'member_id' => $member->id,
        //             'member_name' => $member->first_name ?? null,
        //             'dna_traits' => $dnaTraits->map(function ($trait) {
        //                 return [
        //                     'trait' => $trait->trait,
        //                     'value' => $trait->value,
        //                 ];
        //             })->toArray()
        //         ];
        //     }

        //     // Otherwise, fall back to quiz answers
        //     return [
        //         'member_id' => $member->id,
        //         'member_name' => $member->first_name ?? null,
        //         'answers' => $member->userAnswers->map(function ($answer) {
        //             return [
        //                 'question_id' => $answer->question_id,
        //                 'answer_text' => $answer->answer_text,
        //                 'selected_option_value' => $answer->selected_option_value,
        //             ];
        //         })->toArray(),
        //     ];
        // })->toArray();

        // build the prompt to instruct the AI to return JSON in the required structure
        $prompt = " You are a professional nutritionist and meal planner.

                    Given the recipe titled '{$recipe->title}' with ingredients:
                    " . implode(", ", $originalIngredients) . "

                    And the following family member profiles:
                    " . json_encode($memberProfiles, JSON_PRETTY_PRINT) . "

                    Please generate a personalized ingredient list for each member, grouped by member name and ingredient section.
                    Replace or suggest alternatives for ingredients that may cause allergies or don't fit diet/health goals.

                    Return the output as valid JSON in this exact structure:
                    [
                        {
                            \"member_name\": \"Member Name\",
                            \"section_name\": \"Section Name\",
                            \"ingredients\": [
                                { \"id\": 1, \"name\": \"Ingredient Name\", \"amount\": \"Amount in grams\" }
                                // ...
                            ]
                        }
                        // ...
                    ]
                    Only return the JSON, nothing else.
                    ";

        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model'       => 'gpt-4',
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a meal planning assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens'  => 1000,
            'temperature' => 0.7,
        ]);

        $content = $response->json('choices.0.message.content') ?? null;

        $aiIngredients = null;
        if ($content) {
            $aiIngredients = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($aiIngredients)) {
                $aiIngredients = $this->groupIngredientsByMember($aiIngredients);
            } else {
                $aiIngredients = $content;
            }
        }

        return $aiIngredients;
    }

    protected function groupIngredientsByMember($aiIngredients)
    {
        $grouped = [];

        foreach ($aiIngredients as $item) {
            $memberName = $item['member_name'];
            unset($item['member_name']);

            if (! isset($grouped[$memberName])) {
                $grouped[$memberName] = [
                    'member_name' => $memberName,
                    'sections'    => [],
                ];
            }
            $grouped[$memberName]['sections'][] = [
                'section_name' => $item['section_name'],
                'ingredients'  => $item['ingredients'],
            ];
        }

        // Reset array keys
        return array_values($grouped);
    }
}
