<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRecipe;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RecipeCardController extends Controller
{
    use ApiResponse;
    public function recipe_list(Request $request)
    {
        $perPage = $request->input('per_page', 10); // default 10 per page

        $orders = Order::with([
            'recipes.recipe.category',
            'recipes.recipe.protein',
            'recipes.recipe.calory',
            'recipes.recipe.carb',
            'recipes.recipe.cuisine',
            'recipes.recipe.time_to_clock',
            'recipes.recipe.health_goal',
            'recipes.recipe.instructions',
            'recipes.recipe.ingredientSections.ingredients',
            'order_ingredients.userFamilyMember',
        ])
            ->where('user_id', auth('api')->user()->id)
            ->latest()
            ->paginate($perPage);

        $data = [];

        foreach ($orders as $order) {
            foreach ($order->recipes as $orderRecipe) {
                $recipe = $orderRecipe->recipe;

                if (! $recipe) {
                    continue;
                }

                $ingredients  = $recipe->ingredientSections->flatMap(fn($section) => $section->ingredients)->unique('id')->values();
                $instructions = $recipe->instructions->pluck('description');

                $orderIngredients = $order->order_ingredients->where('recipe_id', $recipe->id);

                $swapGuide = $orderIngredients->groupBy(fn($ingredient) => optional($ingredient->userFamilyMember)->id)
                    ->map(function ($items, $familyMemberId) {
                        $familyMember = $items->first()->userFamilyMember;
                        return [
                            'family_member' => [
                                'id'   => $familyMember->id ?? null,
                                'name' => trim(($familyMember->first_name ?? '') . ' ' . ($familyMember->last_name ?? '')) ?: 'Unknown',
                            ],
                            'ingredients'   => $items->map(fn($ingredient) => [
                                'id'       => $ingredient->id,
                                'original' => $ingredient->original_ingredient,
                                'swapped'  => $ingredient->swapped_ingredient,
                                'reason'   => $ingredient->reason,
                            ])->values(),
                        ];
                    })->values();

                $data[] = [
                    'recipe_id'    => $recipe->id,
                    'name'         => $recipe->title ?? $recipe->name,
                    'image'        => url($recipe->image_url),
                    'category'     => optional($recipe->category)->name,
                    'protein'      => optional($recipe->protein)->name,
                    'calory'       => optional($recipe->calory)->name,
                    'carb'         => optional($recipe->carb)->name,
                    'cuisine'      => optional($recipe->cuisine)->name,
                    'time_to_cook' => optional($recipe->time_to_clock)->name,
                    'health_goal'  => optional($recipe->health_goal)->name,
                    'download_url' => route('api.recipe.download', ['id' => $recipe->id]),
                    'ingredients'  => $ingredients->map(fn($ingredient) => [
                        'id'   => $ingredient->id,
                        'name' => $ingredient->name,
                    ]),
                    'instructions' => $instructions,
                    'swap_guide'   => $swapGuide,
                ];
            }
        }

        return Helper::jsonResponse(true, 'Recipe List with Details Retrieved Successfully', 200, [
            'data'       => array_values($data),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    public function recipe_details($recipe_id)
    {
        $orderRecipe = OrderRecipe::with([
            'recipe.instructions',
            'recipe.ingredientSections.ingredients',
            'order.order_ingredients.userFamilyMember',
        ])->where('recipe_id', $recipe_id)->first();

        if (! $orderRecipe) {
            return Helper::jsonResponse(false, 'Recipe not found', 404, null);
        }

        $recipe = $orderRecipe->recipe;

        // General ingredients list
        $ingredients = $recipe->ingredientSections->flatMap(function ($section) {
            return $section->ingredients;
        })->unique('id')->values();

        // Instructions
        $instructions = $recipe->instructions->pluck('description');

        // Group order_ingredients by userFamilyMember for swap guide
        $orderIngredients = $orderRecipe->order->order_ingredients ?? collect();
        $swapGuide        = $orderIngredients->groupBy(function ($ingredient) {
            return optional($ingredient->userFamilyMember)->id;
        })->map(function ($items, $familyMemberId) {
            $familyMember = $items->first()->userFamilyMember;
            return [

                'family_member' => [
                    'id'   => $familyMember->id ?? null,
                    'name' => $familyMember->first_name . " " . $familyMember->last_name ?? 'Unknown',
                ],

                'ingredients'   => $items->map(function ($ingredient) {
                    return [
                        'id'   => $ingredient->id,
                        'name' => $ingredient->reason,
                    ];
                })->values(),

            ];
        })->values();

        $data = [
            'recipe'       => [
                'id'    => $recipe->id,
                'name'  => $recipe->name,
                'image' => url($recipe->image_url),
            ],
            'ingredients'  => $ingredients->map(function ($ingredient) {
                return [
                    'id'   => $ingredient->id,
                    'name' => $ingredient->name,
                ];
            }),
            'instructions' => $instructions,
            'swap_guide'   => $swapGuide,
        ];

        return Helper::jsonResponse(true, 'Recipe Details Retrieved Successfully', 200, $data);
    }

    public function download_recipe_pdf($recipe_id)
    {
        // Fetch recipe details as in recipe_details()
        $orderRecipe = OrderRecipe::with([
            'recipe.instructions',
            'recipe.ingredientSections.ingredients',
            'order.order_ingredients.userFamilyMember',
        ])->where('recipe_id', $recipe_id)->first();

        if (! $orderRecipe) {
            abort(404, 'Recipe not found');
        }

        $recipe      = $orderRecipe->recipe;
        $ingredients = $recipe->ingredientSections->flatMap(function ($section) {
            return $section->ingredients;
        })->unique('id')->values();

        $instructions = $recipe->instructions->pluck('description');

        $orderIngredients = $orderRecipe->order->order_ingredients ?? collect();
        $swapGuide        = $orderIngredients->groupBy(function ($ingredient) {
            return optional($ingredient->userFamilyMember)->id;
        })->map(function ($items, $familyMemberId) {
            $familyMember = $items->first()->userFamilyMember;
            return [
                'family_member' => [
                    'id'   => $familyMember->id ?? null,
                    'name' => $familyMember->first_name . " " . $familyMember->last_name ?? 'Unknown',
                ],
                'ingredients'   => $items->map(function ($ingredient) {
                    return [
                        'id'   => $ingredient->id,
                        'name' => $ingredient->reason,
                    ];
                })->values(),
            ];
        })->values();

        return view('frontend.layouts.recipe', [
            'recipe'       => [
                'name'  => $recipe->title,
                'image' => $recipe->image_url,
            ],
            'ingredients'  => $ingredients->map(function ($ingredient) {
                return [
                    'name' => $ingredient->name,
                ];
            }),
            'instructions' => $instructions,
            'swap_guide'   => $swapGuide,
        ]);

        // dd($pdf);

        // return $pdf->download('recipe_' . $recipe->id . '.pdf');
    }

    public function order_history()
    {
        $orders = Order::with('recipes')->get();

        if ($orders->isEmpty()) {
            return $this->error([], 'No Delivery Addresses Found', 404);
        }

        $data = $orders->map(function ($order) {
            return [
                'order_id'   => $order->id,
                'item' => $order->recipes()->count('quantity'),
                'order_date' => $order->created_at,
                'status' => $order->status

            ];
        });

        return $this->success($data, 'Delivery Addresses List Retrieved Successfully');
    }
}
