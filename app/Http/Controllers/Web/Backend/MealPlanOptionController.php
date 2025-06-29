<?php
namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Models\MealPlanOption;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MealPlanOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // meal plans
            $data = MealPlanOption::with('meal_plan')->latest()->get();
            return DataTables::of($data)

                ->addIndexColumn()
                 ->addColumn('plan', function ($data) {
                    return $data->meal_plan->name;
                })
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
        return view("backend.layouts.meal_item_option.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $meal_plans = MealPlan::get();

        return view('backend.layouts.meal_item_option.create', compact('meal_plans'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $rules = [
            'meal_plan_id'      => 'required|exists:meal_plans,id',
            'recipes_per_week'  => 'required|integer|min:1|max:7',
            'price_per_serving' => 'required|numeric|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Start DB transaction
            DB::beginTransaction();

            MealPlanOption::create([
                'meal_plan_id'         => $request->meal_plan_id,
                'recipes_per_week'         => $request->recipes_per_week,
                'price_per_serving' => $request->price_per_serving,
                'is_recommanded' => $request->has('is_recommanded'), // <-- auto true/false
            ]);

            DB::commit();

            return redirect()->route('admin.meal_plan_option.index')->with('t-success', 'Meal plan option created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('t-error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $meal_plan_option = MealPlanOption::findOrFail($id);
        $meal_plans = MealPlan::get();


        return view('backend.layouts.meal_item_option.edit', compact(
            'meal_plans',
            'meal_plan_option',
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
            'meal_plan_id'      => 'required|exists:meal_plans,id',
            'recipes_per_week'  => 'required|integer|min:1|max:7',
            'price_per_serving' => 'required|numeric|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $recipe               = MealPlanOption::findOrFail($id);
            $recipe->meal_plan_id         = $request->meal_plan_id;
            $recipe->recipes_per_week         = $request->recipes_per_week;
            $recipe->price_per_serving = $request->price_per_serving;
            $recipe->is_recommanded = $request->is_recommanded;

            $recipe->save();

            DB::commit();

            return redirect()->route('admin.meal_plan_option.index')->with('t-success', 'Meal option updated successfully.');
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

            $meal_plan = MealPlanOption::findOrFail($id);
            if (! $meal_plan) {
                return response()->json([
                    'status'  => 't-error',
                    'message' => 'Meal plan not found.',
                ], 404);
            }

            // use soft delete
            $meal_plan->delete();

            return response()->json([
                'status'  => 't-success',
                'message' => 'Meal option deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 't-error',
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function status(int $id): JsonResponse
    {
        $data = Recipe::findOrFail($id);
        if (! $data) {
            return response()->json([
                'status'  => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->delete();

        return response()->json([
            'status'  => 't-success',
            'message' => 'Your action was successful!',
        ]);
    }
}
