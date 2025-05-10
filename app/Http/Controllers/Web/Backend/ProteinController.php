<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Protein;
use App\Repositories\Interfaces\ProteinRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProteinController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Protein::all();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                <a href="#" type="button" onclick="goToEdit(' . $data->id . ')" class="btn btn-primary fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-edit"></i>
                                </a>

                                <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make();
        }
        return view("backend.layouts.protein.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.protein.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        try {
            $protein = new Protein();
            $protein->name = $request->name;
            $protein->save();

            session()->put('t-success', 'Protein created successfully');
        } catch (Exception $e) {
            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.protein.index')->with('t-success', 'Protein created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category, $id)
    {
        $category = $this->proteinRepository->find($id);
        return view('backend.layouts.protein.edit', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $protein = Protein::find($id);
        return view('backend.layouts.protein.edit', compact('protein'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        try {
            $protein = Protein::find($id);
            $protein->name = $request->name;
            $protein->save();

            session()->put('t-success', 'Protein updated successfully');
        } catch (Exception $e) {
            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.protein.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
           $protein = Protein::find($id);

           $protein->delete();

            return response()->json([
                'status' => 't-success',
                'message' => 'Your action was successful!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function status(int $id): JsonResponse
    {
        try {
            $this->proteinRepository->status($id);
            return response()->json([
                'status' => 't-success',
                'message' => 'Your action was successful!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
