<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    use ApiResponse;



    // list user profile
    public function index(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }
        // Append additional attributes if needed

        return $this->success($user, 'User profile retrieved successfully');
    }

    // update user profile
    public function update(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $data = $request->only(['first_name', 'last_name', 'phone_number','age', 'height', 'weight']);

        // Validate the data
        $validator = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
    
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        // Update user profile
        
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
            'age' => $data['age'] ?? $user->age,
            'height' => $data['height'] ?? $user->height,
            'weight' => $data['weight'] ?? $user->weight,
        ]);

        return $this->success($user, 'User profile updated successfully');
    }
}
