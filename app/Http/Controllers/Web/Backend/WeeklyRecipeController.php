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

        $weekStarts = WeeklyRecipe::select('week_start')
            ->groupBy('week_start')
            ->orderBy('week_start', 'desc')
            ->get()
            ->pluck('week_start');

        $weeks = $weekStarts->map(function ($week_start) {
            return (object)[
                'week_start' => $week_start,
                'recipes' => WeeklyRecipe::where('week_start', $week_start)->with('recipe')->get()->pluck('recipe'),
                
                'id' => WeeklyRecipe::where('week_start', $week_start)->first()->id,
            ];
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
       
        $rules = [
            'week_start' => 'required|date',
            'recipe_ids' => 'required|array|min:1',
            'recipe_ids.*' => 'exists:recipes,id',
        ];

        

        Validator::make($request->all(), $rules);

        DB::beginTransaction();

        try {
            // delete week 
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {

            $weekly_recipe = WeeklyRecipe::where('recipe_id', $id)->first();
            if (!$weekly_recipe) {
                return response()->json([
                    'status' => 't-error',
                    'message' => 'Weekly recipe not found.',
                ], 404);
            }

            // use soft delete
            $weekly_recipe->delete();
            return redirect()->back()->with('success', 'Weekly recipe deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to assign recipes: ' . $e->getMessage());
        }
    }



    public function add_recipe(Request $request, $weekId)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
        ]);


        $week = WeeklyRecipe::findOrFail($weekId);


        $alreadyAssigned = WeeklyRecipe::where('week_start', $week->week_start)
            ->where('recipe_id', $request->recipe_id)
            ->exists();

        if ($alreadyAssigned) {
            return back()->with('error', 'This recipe is already assigned to the selected week.');
        }

        // Assign the new recipe to the same week_start
        WeeklyRecipe::create([
            'week_start' => $week->week_start,
            'recipe_id' => $request->recipe_id,
        ]);

        return back()->with('success', 'Recipe added to the week successfully.');
    }
}
