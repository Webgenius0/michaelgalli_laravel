<?php
namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class OrderManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Order::with(['user.deliveryAddresses', 'user.billing_information', 'user.plan_cart', 'recipes'])->orderBy('id', 'desc')->get();

            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('user', function ($data) {
                    if (! $data->user) {
                        return '<span class="text-danger">User not found</span>';
                    }

                    return "Name: <strong>" . e($data->user->first_name . ' ' . $data->user->last_name) . "</strong><br>" .
                    "Email: <a href='mailto:" . e($data->user->email) . "'>" . e($data->user->email) . "</a><br>" .
                    "Phone: <a href='tel:" . e($data->user->phone_number) . "'>" . e($data->user->phone_number) . "</a>";
                })

                ->addColumn('billing_info', function ($data) {
                    $billing = $data->user?->billing_information;

                    if (! $billing) {
                        return '<span class="text-muted">No billing info</span>';
                    }

                    return "Name: <strong>" . e($billing->full_name) . "</strong><br>" .
                    "Email: <a href='mailto:" . e($billing->email) . "'>" . e($billing->email) . "</a><br>" .
                    "Phone: <a href='tel:" . e($billing->phone) . "'>" . e($billing->phone) . "</a><br>" .
                    "City: " . e($billing->city) . "<br>" .
                    "Address: " . e($billing->address ?? 'N/A');
                })

                ->addColumn('week', function ($data) {
                    return $data->week_start->format('M d, Y');
                })

                ->addColumn('total_price', function ($data) {
                    return $data->user->plan_cart ? $data->user->plan_cart->total_price . " AED" : 0;
                })

                ->addColumn('total_recipe', function ($data) {
                    return $data->recipes->count();
                })

                ->addColumn('status', function ($data) {
                    $statuses = [
                        'pending'    => 'color: #ffc107;', // yellow
                        'processing' => 'color: #17a2b8;', // blue
                        'completed'  => 'color: #28a745;', // green
                        'cancelled'  => 'color: #dc3545;', // red
                    ];

                    $options = '';
                    foreach ($statuses as $status => $style) {
                        $selected = $data->status === $status ? 'selected' : '';
                        $label    = ucfirst($status); // Makes 'pending' => 'Pending'
                        $options .= "<option value='{$status}' style='{$style}' {$selected}>{$label}</option>";
                    }

                    return '<select class="form-control form-control-sm status-update" data-id="' . $data->id . '">' . $options . '</select>';
                })

                ->addColumn('action', function ($data) {
                    $encryptedId = Crypt::encryptString($data->id);

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                               <a href="#" type="button" onclick="goToOpen(`' . $encryptedId . '`)" class="btn btn-success fs-14 text-white" title="View">
                                    <i class="fe fe-eye"></i>
                                </a>



                                <a href="#" type="button" onclick="showDeleteConfirm(`' . $encryptedId . '`)" class="btn btn-danger fs-14 text-white" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>

                            </div>';
                })
                ->rawColumns(['user', 'billing_info', 'week', 'total_price', 'total_recipe', 'status', 'action'])
                ->make();
        }
        return view("backend.layouts.orders.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view(
            'backend.layouts.orders.create'
        );
    }

    public function show($encryptedId)
    {

        $id = Crypt::decryptString($encryptedId);

        $order = $recipe = Order::with([
            'user.deliveryAddresses',
            'user.billing_information',
            'user.plan_cart',
            'recipes.recipe',
            'order_ingredients.recipe',
            'order_ingredients.userFamilyMember',
        ])->findOrFail($id);

        return view('backend.layouts.orders.show', compact('order'));
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

    public function status(int $id, Request $request): JsonResponse
    {
        $data = Order::findOrFail($id);
        if (! $data) {
            return response()->json([
                'status'  => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->status = $request->status;
        $data->save();

        return response()->json([
            'status'  => 't-success',
            'message' => 'Your action was successful!',
        ]);
    }
}
