<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use App\Models\Carb;
use App\Models\Post;
use App\Models\Image;
use App\Models\Recipe;
use App\Helpers\Helper;
use App\Models\Cuisine;
use App\Models\Protein;
use App\Models\Calories;
use App\Models\Category;
use App\Models\HealthGoal;
use App\Models\Subcategory;
use App\Models\TimeToClock;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {


            $data = Recipe::with('ingredientSections.ingredients')->orderBy('id', 'desc')->get();


            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('ingredients', function ($recipe) {
                    $ingredientsList = [];

                    foreach ($recipe->ingredientSections as $section) {
                        foreach ($section->ingredients as $ingredient) {
                            $ingredientsList[] = $ingredient->name . ' (' . $ingredient->amount . ')';
                        }
                    }

                    // Join ingredients by comma, limit length to 50 chars for neatness
                    return Str::limit(implode(', ', $ingredientsList), 80);
                })


                ->addColumn('title', function ($data) {
                    return Str::limit($data->title, 20);
                })
                ->addColumn('short_description', function ($data) {
                    return Str::limit($data->short_description, 40);
                })


                ->addColumn('image_url', function ($data) {
                    $url = asset($data->image_url && file_exists(public_path($data->image_url)) ? $data->image_url : 'default/logo.svg');
                    return '<img src="' . $url . '" alt="image" style="width: 50px; max-height: 100px; margin-left: 20px;">';
                })

                ->addColumn('action', function ($data) {
                    $encryptedId = Crypt::encryptString($data->id);

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                <a href="#" type="button" onclick="goToEdit(`' . $encryptedId . '`)" class="btn btn-primary fs-14 text-white" title="Edit">
                                    <i class="fe fe-edit"></i>
                                </a>

                                <a href="#" type="button" onclick="goToOpen(`' . $encryptedId . '`)" class="btn btn-success fs-14 text-white" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>

                                <a href="#" type="button" onclick="showDeleteConfirm(`' . $encryptedId . '`)" class="btn btn-danger fs-14 text-white" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>

                            </div>';
                })
                ->rawColumns(['title', 'image_url', 'short_description', 'ingredients', 'action'])
                ->make();
        }
        return view("backend.layouts.recipe.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();

        $proteins = Protein::all();
        $calories = Calories::all();
        $carbs = Carb::all();
        $cuisines = Cuisine::all();
        $health_goals = HealthGoal::all();
        $time_to_cooks = TimeToClock::all();
        $categories = Category::all();


        return view(
            'backend.layouts.recipe.create',
            compact(
                'categories',
                'proteins',
                'calories',
                'carbs',
                'cuisines',
                'health_goals',
                'time_to_cooks'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // dd($request->all());
        // Validation rules including nested arrays and files
        $rules = [
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'protein_id' => 'nullable|exists:proteins,id',
            'calory_id' => 'nullable|exists:calories,id',
            'carb_id' => 'nullable|exists:carbs,id',
            'cuisine_id' => 'nullable|exists:cuisines,id',
            'health_goal_id' => 'nullable|exists:health_goals,id',
            'time_to_clock_id' => 'nullable|exists:time_to_clocks,id',
            // 'category_id' => 'nullable|exists:categories,id',

            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.order' => 'required|integer',
            'sections.*.ingredients' => 'required|array',
            'sections.*.ingredients.*.name' => 'required|string|max:255',
            'sections.*.ingredients.*.amount' => 'required|string|max:255',
            'sections.*.ingredients.*.is_highlighted' => 'nullable|boolean',


            'instructions' => 'required|array|min:1',
            'instructions.*.title' => 'required|string|max:255',
            'instructions.*.step_number' => 'required|integer|min:1',
            'instructions.*.description' => 'required|string',
            'instructions.*.image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Start DB transaction
            DB::beginTransaction();

            // Handle main thumbnail image upload
            $thumbnailPath = null;
            if ($request->hasFile('image_url')) {
                $thumbnailPath = Helper::fileUpload($request->file('image_url'), 'recipes', 'thumbnail_' . Str::random(10));
            }

            // Create recipe
            $recipe = Recipe::create([
                'title' => $request->title,
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
                'image_url' => $thumbnailPath,
                'protein_id' => $request->protein_id,
                'calories_id' => $request->calory_id,
                'carb_id' => $request->carb_id,
                'cuisine_id' => $request->cuisine_id,
                'health_goal_id' => $request->health_goal_id,
                'time_to_clock_id' => $request->time_to_clock_id,
                // 'category_id' => $request->category_id,
            ]);

            foreach ($request->sections as $sectionData) {
                $section = $recipe->ingredientSections()->create([
                    'title' => $sectionData['title'],
                    'order' => $sectionData['order'],
                ]);

                $ingredients = [];
                foreach ($sectionData['ingredients'] as $ingredient) {
                    $ingredients[] = [
                        'name' => $ingredient['name'],
                        'amount' => $ingredient['amount'],
                        'is_highlighted' => $ingredient['is_highlighted'] ?? false,
                    ];
                }

                $section->ingredients()->createMany($ingredients);
            }

            // Save Instructions
            foreach ($request->instructions as $instructionIndex => $instructionData) {
                $instructionImagePath = null;
                if (isset($instructionData['image_url']) && is_file($instructionData['image_url'])) {
                    $instructionImagePath = Helper::fileUpload($instructionData['image_url'], 'instructions', 'instruction_' . Str::random(10));
                } elseif ($request->hasFile("instructions.$instructionIndex.image_url")) {
                    $file = $request->file("instructions.$instructionIndex.image_url");
                    $instructionImagePath = Helper::fileUpload($file, 'instructions', 'instruction_' . Str::random(10));
                }

                $recipe->instructions()->create([
                    'title' => $instructionData['title'],
                    'step_number' => $instructionData['step_number'],
                    'description' => $instructionData['description'],
                    'image_url' => $instructionImagePath,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.recipe.index')->with('t-success', 'Recipe created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('t-error', 'Error: ' . $e->getMessage())->withInput();
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



        return view('backend.layouts.recipe.show', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);


        $recipe = Recipe::with([
            'ingredientSections.ingredients',
            'instructions',
        ])->findOrFail($id);



        $proteins = Protein::all();
        $calories = Calories::all();
        $carbs = Carb::all();
        $cuisines = Cuisine::all();
        $health_goals = HealthGoal::all();
        $time_to_cooks = TimeToClock::all();

        return view('backend.layouts.recipe.edit', compact(
            'recipe',
            'proteins',
            'calories',
            'carbs',
            'cuisines',
            'health_goals',
            'time_to_cooks',
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
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'protein_id' => 'nullable|exists:proteins,id',
            'calory_id' => 'nullable|exists:calories,id',
            'carb_id' => 'nullable|exists:carbs,id',
            'cuisine_id' => 'nullable|exists:cuisines,id',
            'health_goal_id' => 'nullable|exists:health_goals,id',
            'time_to_clock_id' => 'nullable|exists:time_to_clocks,id',

            'sections' => 'required|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.order' => 'required|integer',
            'sections.*.ingredients' => 'required|array',
            'sections.*.ingredients.*.name' => 'required|string|max:255',
            'sections.*.ingredients.*.amount' => 'required|string|max:255',
            'sections.*.ingredients.*.is_highlighted' => 'nullable|boolean',

            'instructions' => 'required|array|min:1',
            'instructions.*.title' => 'required|string|max:255',
            'instructions.*.step_number' => 'required|integer|min:1',
            'instructions.*.description' => 'required|string',
            'instructions.*.image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $recipe = Recipe::findOrFail($id);

            // Handle main image upload if exists
            if ($request->hasFile('image_url')) {
                // Delete old image
                if ($recipe->image_url && file_exists(public_path($recipe->image_url))) {
                    unlink(public_path($recipe->image_url));
                }
                $thumbnailPath = Helper::fileUpload($request->file('image_url'), 'recipes', 'thumbnail_' . Str::random(10));
                $recipe->image_url = $thumbnailPath;
            }

            // Update recipe main info
            $recipe->title = $request->title;
            $recipe->short_description = $request->short_description;
            $recipe->long_description = $request->long_description;
            $recipe->protein_id = $request->protein_id;
            $recipe->calories_id = $request->calory_id;
            $recipe->carb_id = $request->carb_id;
            $recipe->cuisine_id = $request->cuisine_id;
            $recipe->health_goal_id = $request->health_goal_id;
            $recipe->time_to_clock_id = $request->time_to_clock_id;
            $recipe->save();

            // Delete existing ingredient sections and ingredients, then recreate to simplify
            $recipe->ingredientSections()->each(function ($section) {
                $section->ingredients()->delete();
                $section->delete();
            });

            foreach ($request->sections as $sectionData) {
                $section = $recipe->ingredientSections()->create([
                    'title' => $sectionData['title'],
                    'order' => $sectionData['order'],
                ]);

                $ingredients = [];
                foreach ($sectionData['ingredients'] as $ingredient) {
                    $ingredients[] = [
                        'name' => $ingredient['name'],
                        'amount' => $ingredient['amount'],
                        'is_highlighted' => $ingredient['is_highlighted'] ?? false,
                    ];
                }
                $section->ingredients()->createMany($ingredients);
            }

            // Delete existing instructions, then recreate
            $recipe->instructions()->delete();

            foreach ($request->instructions as $instructionIndex => $instructionData) {
                $instructionImagePath = null;

                if ($request->hasFile("instructions.$instructionIndex.image_url")) {
                    $file = $request->file("instructions.$instructionIndex.image_url");
                    $instructionImagePath = Helper::fileUpload($file, 'instructions', 'instruction_' . Str::random(10));
                }

                $recipe->instructions()->create([
                    'title' => $instructionData['title'],
                    'step_number' => $instructionData['step_number'],
                    'description' => $instructionData['description'],
                    'image_url' => $instructionImagePath,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.recipe.index')->with('t-success', 'Recipe updated successfully.');
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

            $recipe = Recipe::with(['ingredientSections.ingredients', 'instructions'])->findOrFail($id);

            
            if ($recipe->image_url && file_exists(public_path($recipe->image_url))) {
                unlink(public_path($recipe->image_url));
            }

            
            foreach ($recipe->ingredientSections as $section) {
                
                $section->ingredients()->delete();
                $section->delete();
            }

            
            foreach ($recipe->instructions as $instruction) {
                if ($instruction->image_url && file_exists(public_path($instruction->image_url))) {
                    unlink(public_path($instruction->image_url));
                }
                $instruction->delete();
            }

            
            $recipe->delete();

            return response()->json([
                'status' => 't-success',
                'message' => 'Recipe deleted successfully!',
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
