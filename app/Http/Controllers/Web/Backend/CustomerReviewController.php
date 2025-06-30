<?php
namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Calories;
use App\Models\Carb;
use App\Models\Cuisine;
use App\Models\CustomerReview;
use App\Models\HealthGoal;
use App\Models\Protein;
use App\Models\Recipe;
use App\Models\TimeToClock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CustomerReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = CustomerReview::orderBy('id', 'desc')->get();

            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('content', function ($data) {
                    return Str::limit($data->content, 40);
                })

                ->addColumn('customer_image', function ($data) {
                    $url = asset($data->customer_image && file_exists(public_path($data->customer_image)) ? $data->customer_image : 'default/logo.svg');
                    return '<img src="' . $url . '" alt="image" style="width: 50px; max-height: 100px; margin-left: 20px;">';
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
                ->rawColumns(['title', 'customer_image', 'action'])
                ->make();
        }
        return view("backend.layouts.review.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view(
            'backend.layouts.review.create'
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
            'customer_name' => 'required|string|max:255',
            'content'       => 'required',
            'rating'        => 'required',

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            $customer_image = null;
            if ($request->hasFile('customer_image')) {
                $customer_image = Helper::fileUpload($request->file('customer_image'), 'customer', 'image_' . Str::random(10));
            }

            // Create recipe
            $recipe = CustomerReview::create([
                'customer_name'  => $request->customer_name,
                'content'        => $request->content,
                'rating'         => $request->rating,
                'customer_image' => $customer_image,

            ]);

            return redirect()->route('admin.review.index')->with('t-success', 'Review created successfully.');
        } catch (\Exception $e) {

            return redirect()->back()->with('t-error', 'Error: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $review = CustomerReview::findOrFail($id);

        return view('backend.layouts.review.edit', compact('review', 'encryptedId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);

        $rules = [
            'customer_name'                                   => 'required|string|max:255',
            'customer_image'                               => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            $review = CustomerReview::findOrFail($id);

            // Handle main image upload if exists
            if ($request->hasFile('customer_image')) {
                // Delete old image
                if ($review->customer_image && file_exists(public_path($review->customer_image))) {
                    unlink(public_path($review->customer_image));
                }
                $thumbnailPath     = Helper::fileUpload($request->file('customer_image'), 'customer', 'image_' . Str::random(10));
                $review->customer_image = $thumbnailPath;
            }

            // Update review main info
            $review->customer_name             = $request->customer_name;
            $review->content = $request->content;
            $review->rating = $request->rating;

            $review->save();


            return redirect()->route('admin.review.index')->with('t-success', 'review updated successfully.');
        } catch (\Exception $e) {

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

            $review = CustomerReview::findOrFail($id);

            if ($review->customer_image && file_exists(public_path($review->customer_image))) {
                unlink(public_path($review->customer_image));
            }

            $review->delete();

            return response()->json([
                'status'  => 't-success',
                'message' => 'Review deleted successfully!',
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
