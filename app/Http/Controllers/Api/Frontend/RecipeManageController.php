<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Recipe;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecipeManageController extends Controller
{


    public function recipe_list(Request $request)
    {
        $recipes = Recipe::with([
            // 'ingredientSections.ingredients',
            'protein',
            'calory',
            'carb',
            'cuisine',
            'time_to_clock',
            'health_goal',
            // 'instructions' => fn($q) => $q->orderBy('step_number')
        ]);

        $recipes->when($request->filled('protein_id'), function ($query) use ($request) {
            $query->where('protein_id', $request->protein_id);
        });

        $recipes->when($request->filled('calory_id'), function ($query) use ($request) {
            // assuming calories_id is a foreign key, so exact match
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
                        })

                    ;
                });
            }
        });

        $list = $recipes->orderBy('created_at', 'desc')->get();


        return Helper::jsonResponse(true, 'Recipe List Retrive Successfully', 200, $list);
    }


    public function recipe_details($id)
    {
        $recipe = Recipe::with([
            'ingredientSections.ingredients',
            'protein',
            'calory',
            'carb',
            'cuisine',
            'time_to_clock',
            'health_goal',
            'instructions' => fn($q) => $q->orderBy('step_number')
        ])->find($id);

        return Helper::jsonResponse(true, 'Recipe Details Retrive Successfully', 200, $recipe);
    }
}
