<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Order;
use App\Models\Recipe;
use App\Helpers\Helper;
use App\Models\OrderRecipe;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class RecipeCardController extends Controller
{
    public function recipe_list()
    {
        $orders = Order::with([
            'recipes.recipe.protein',
            'recipes.recipe.calory',
            'recipes.recipe.carb',
            'recipes.recipe.cuisine',
            'recipes.recipe.time_to_clock',
            'recipes.recipe.health_goal',
        ])->get();

        $formatted = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'recipes' => $order->recipes->map(function ($orderRecipe) {
                    $recipe = $orderRecipe->recipe;

                    return [
                        'recipe_id' => $recipe->id,
                        'name' => $recipe->name,
                        'image' => url($recipe->image_url),
                        'protein' => optional($recipe->protein)->name,
                        'calory' => optional($recipe->calory)->name,
                        'carb' => optional($recipe->carb)->name,
                        'cuisine' => optional($recipe->cuisine)->name,
                        'time_to_cook' => optional($recipe->time_to_clock)->name,
                        'health_goal' => optional($recipe->health_goal)->name,
                        'download_url' => route('api.recipe.download', ['id' => $recipe->id]),
                    ];
                }),
            ];
        });

        return Helper::jsonResponse(true, 'Order Recipe List Retrieved Successfully', 200, $formatted);
    }




    public function recipe_details($recipe_id)
    {
        $orderRecipe = OrderRecipe::with([
            'recipe.instructions',
            'recipe.ingredientSections.ingredients',
            'order.order_ingredients.userFamilyMember'
        ])->where('recipe_id', $recipe_id)->first();

        if (!$orderRecipe) {
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
        $swapGuide = $orderIngredients->groupBy(function ($ingredient) {
            return optional($ingredient->userFamilyMember)->id;
        })->map(function ($items, $familyMemberId) {
            $familyMember = $items->first()->userFamilyMember;
            return [

                'family_member' => [
                    'id' => $familyMember->id ?? null,
                    'name' => $familyMember->first_name . " " . $familyMember->last_name ?? 'Unknown'
                ],

                'ingredients' => $items->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->reason,
                    ];
                })->values()

            ];
        })->values();

        $data = [
            'recipe' => [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'image' => url($recipe->image_url),
            ],
            'ingredients' => $ingredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                ];
            }),
            'instructions' => $instructions,
            'swap_guide' => $swapGuide
        ];

        return Helper::jsonResponse(true, 'Recipe Details Retrieved Successfully', 200, $data);
    }



    public function download_recipe_pdf($recipe_id)
    {
        // Fetch recipe details as in recipe_details()
        $orderRecipe = OrderRecipe::with([
            'recipe.instructions',
            'recipe.ingredientSections.ingredients',
            'order.order_ingredients.userFamilyMember'
        ])->where('recipe_id', $recipe_id)->first();

        if (!$orderRecipe) {
            abort(404, 'Recipe not found');
        }

        $recipe = $orderRecipe->recipe;
        $ingredients = $recipe->ingredientSections->flatMap(function ($section) {
            return $section->ingredients;
        })->unique('id')->values();

        $instructions = $recipe->instructions->pluck('description');

        $orderIngredients = $orderRecipe->order->order_ingredients ?? collect();
        $swapGuide = $orderIngredients->groupBy(function ($ingredient) {
            return optional($ingredient->userFamilyMember)->id;
        })->map(function ($items, $familyMemberId) {
            $familyMember = $items->first()->userFamilyMember;
            return [
                'family_member' => [
                    'id' => $familyMember->id ?? null,
                    'name' => $familyMember->first_name . " " . $familyMember->last_name ?? 'Unknown'
                ],
                'ingredients' => $items->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->reason,
                    ];
                })->values()
            ];
        })->values();

        return view ('frontend.layouts.recipe', [
            'recipe' => [
                'name' => $recipe->title,
                'image' =>$recipe->image_url,
            ],
            'ingredients' => $ingredients->map(function ($ingredient) {
                return [
                    'name' => $ingredient->name,
                ];
            }),
            'instructions' => $instructions,
            'swap_guide' => $swapGuide
        ]);

        // dd($pdf);

        // return $pdf->download('recipe_' . $recipe->id . '.pdf');
    }
}
