<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    use ApiResponse;

    // list user profile
    public function index(Request $request)
    {
        $user = auth('api')->user();

        if (! $user) {
            return $this->error([], 'User not found', 404);
        }
        // Append additional attributes if needed

        return $this->success($user, 'User profile retrieved successfully');
    }

    // update user profile
    public function update(Request $request)
    {
        $user = auth('api')->user();

        if (! $user) {
            return $this->error([], 'User not found', 404);
        }

        $data = $request->only(['first_name', 'last_name', 'phone_number', 'age', 'height', 'weight']);

        // Validate the data
        $validator = Validator::make($data, [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'age'          => 'nullable|integer|min:0',
            'height'       => 'nullable|numeric|min:0',
            'weight'       => 'nullable|numeric|min:0',

        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        // Update user profile

        $user->update([
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
            'age'          => $data['age'] ?? $user->age,
            'height'       => $data['height'] ?? $user->height,
            'weight'       => $data['weight'] ?? $user->weight,
        ]);

        return $this->success($user, 'User profile updated successfully');
    }

    public function profile_update(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = auth('api')->user();
        // dd($user);

        if (! $user) {
            return $this->error([], 'No user found', 404);
        }

        $avatarPath = '';

        if ($request->hasFile('avatar')) {
            $avatarPath = Helper::uploadImage($request->file('avatar'), 'users');
        }

        // dd($avatarPath);

        $user->update([

            'avatar' => $avatarPath ? $avatarPath : $user->avatar,

        ]);

        // dd($user);

        $data = [
            'avatar' => $user->avatar ? url($user->avatar) : '',
        ];

        return $this->success($data, 'Profile Updated successfully ', 200);
    }


    // change password
    public function changePassword(Request $request)
    {

        $user = auth('api')->user();
        if (! $user) {
            return $this->error([], 'User not found', 404);
        }
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        if (! password_verify($request->current_password, $user->password)) {
            return $this->error([], 'Current password is incorrect', 422);
        }
        $user->update([
            'password' => bcrypt($request->new_password),
        ]);
        return $this->success([], 'Password changed successfully', 200);

    }

}
