<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Recipe;
use App\Models\MealPlan;
use App\Models\WeeklyRecipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class WeeklyRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {


            // meal plans
            $data = MealPlan::latest()->get();
            return DataTables::of($data)

                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $encryptedId = Crypt::encryptString($data->id);

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                <a href="#" type="button" onclick="goToEdit(`' . $encryptedId . '`)" class="btn btn-primary fs-14 text-white" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>

                                

                                <a href="#" type="button" onclick="showDeleteConfirm(`' . $encryptedId . '`)" class="btn btn-danger fs-14 text-white" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>

                            </div>';
                })
                ->rawColumns(['action'])
                ->make();
        }
        return view("backend.layouts.weekly_recipe.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $recipes = Recipe::all();
        $weeks = WeeklyRecipe::select('week_start')
            ->groupBy('week_start')
            ->orderBy('week_start', 'desc')
            ->get()
            ->map(function ($week) {
                $week->recipes = WeeklyRecipe::where('week_start', $week->week_start)->with('recipe')->get()->pluck('recipe');
                return $week;
            });

        return view('backend.layouts.weekly_recipe.create', compact(
            'recipes',
            'weeks'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
            'recipe_ids' => 'required|array|min:1',
            'recipe_ids.*' => 'exists:recipes,id',
        ]);

        DB::beginTransaction();
        try {
            // Delete old entries for the same week if re-assigning
            WeeklyRecipe::where('week_start', $request->week_start)->delete();

            foreach ($request->recipe_ids as $recipeId) {
                WeeklyRecipe::create([
                    'week_start' => $request->week_start,
                    'recipe_id' => $recipeId,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Recipes assigned for the week.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign recipes: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($encryptedId)
    {


        $id = Crypt::decryptString($encryptedId);


        $recipe = Recipe::with([
            'ingredientSections.ingredients',
            'protein',
            'calory',
            'carb',
            'cuisine',
            'time_to_clock',
            'health_goal',
            'instructions' => fn($q) => $q->orderBy('step_number')
        ])->findOrFail($id);



        return view('backend.layouts.weekly_recipe.show', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);


        $meal_plan = MealPlan::findOrFail($id);


        return view('backend.layouts.weekly_recipe.edit', compact(
            'meal_plan',
            'encryptedId'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $rules = [
            'name' => 'required|string|max:255',
            'people' => 'required|integer|min:1',
            'recipes_per_week' => 'required|integer|min:1',
            'price_per_serving' => 'required|numeric|min:0',
            'stripe_price_id' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $recipe = MealPlan::findOrFail($id);
            $recipe->name = $request->name;
            $recipe->people = $request->people;
            $recipe->recipes_per_week = $request->recipes_per_week;
            $recipe->price_per_serving = $request->price_per_serving;
            $recipe->stripe_price_id = $request->stripe_price_id;
            $recipe->save();

            DB::commit();

            return redirect()->route('admin.meal_plan.index')->with('t-success', 'Recipe updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('t-error', 'Error: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);

            $meal_plan = MealPlan::findOrFail($id);
            if (!$meal_plan) {
                return response()->json([
                    'status' => 't-error',
                    'message' => 'Meal plan not found.',
                ], 404);
            }

            // use soft delete
            $meal_plan->delete();

            return response()->json([
                'status' => 't-success',
                'message' => 'Meal deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function status(int $id): JsonResponse
    {
        $data = Recipe::findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->delete();

        return response()->json([
            'status' => 't-success',
            'message' => 'Your action was successful!',
        ]);
    }
}
