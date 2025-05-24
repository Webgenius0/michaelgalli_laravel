<?php

namespace App\Http\Controllers\Api\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeliveryAddressController extends Controller
{
    use \App\Traits\ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $addresses = $request->user()->deliveryAddresses;

        if ($addresses->isEmpty()) {
            return $this->error([], 'No Delivery Addresses Found', 404);
        }

        return $this->success($addresses, 'Delivery Addresses List Retrieved Successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'landmark' => 'nullable|string|max:255',
            'is_default' => 'boolean'
        ]);

        if ($validated->fails()) {
            return $this->error([], $validated->errors()->first(), 422);
        }

        // Check if the user already has a delivery address
        $user = $request->user();
        $existingAddress = $user->deliveryAddresses()->first();

        $data = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'landmark' => $request->landmark ?? null,
            'is_default' => $request->is_default ?? false,
        ];

        if ($existingAddress) {
            // Update the existing address
            $existingAddress->update($data);
            $address = $existingAddress;
        } else {
            // Create a new address
            $address = $user->deliveryAddresses()->create($data);
        }

        return $this->success($address, 'Delivery Address Saved Successfully');
    }
}
