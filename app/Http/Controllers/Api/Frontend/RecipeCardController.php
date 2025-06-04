<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Recipe;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRecipe;

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
                    ];
                }),
            ];
        });

        return Helper::jsonResponse(true, 'Order Recipe List Retrieved Successfully', 200, $formatted);
    }


    public function recipe_details($recipe_id)
    {
        
        $recipe = OrderRecipe::with([
            'recipe.instructions',
            'recipe.ingredientSections.ingredients'
            ,'order.order_ingredients'
        ])->where('recipe_id', $recipe_id)->first();

        if (!$recipe) {
            return Helper::jsonResponse(false, 'Recipe not found', 404, null);
        }

       

        return Helper::jsonResponse(true, 'Recipe Details Retrieved Successfully', 200, $recipe);
    }
    
}
