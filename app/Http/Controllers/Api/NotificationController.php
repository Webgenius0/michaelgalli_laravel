<?php
namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Mail\SupportMail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Events\TestNotificationEvent;
use App\Notifications\TestNotification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use ApiResponse;


    public function test()
    {

        $user  = auth('api')->user();
        $admin = User::role('admin', 'web')->first();

        $notiData = [
            'user_id' => $user->id,
            'title'   => 'Test Notification Title.',
            'body'    => 'Your Test Notification Body.',
            'icon'    => config('settings.logo'),
        ];

        $admin->notify(new TestNotification($notiData, $admin->id));

        if (config('settings.reverb') == 'on') {
            broadcast(new TestNotificationEvent($notiData, $admin->id))->toOthers();
        }

        return true;
    }

    public function index()
    {
        try {
            $notifications = auth('api')->user()->unreadNotifications;
            return response()->json([
                'status'  => true,
                'message' => 'All Notifications',
                'code'    => 200,
                'data'    => $notifications,
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back();
        }
    }
    public function readSingle($id)
    {
        try {
            $notification = auth('api')->user()->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
            }
            return response()->json([
                'status'  => true,
                'message' => 'Single Notification',
                'code'    => 200,
                'data'    => $notification,
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back();
        }
    }
    public function readAll()
    {
        try {
            auth('api')->user()->notifications->markAsRead();
            return response()->json([
                'status'  => true,
                'message' => 'All Notifications Marked As Read',
                'code'    => 200,
                'data'    => null,
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back();
        }
    }

    public function contact_us(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:100',
            'email'   => 'required',
            'message' => 'required|string',
            'phone' => 'nullable|string',

        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {

            $name    = $request->name;
            $email   = $request->email;
            $message = $request->message;
            $phone = $request->phone;

            // Get admin email address (not the user object)
            $admin_email = User::where('email', "admin@admin.com")->first()?->email ?? config('mail.from.address');

            Mail::to($admin_email)->queue(new SupportMail($name, $email, $message, $phone));

            $data = [
                'name'    => $name,
                'phone' => $phone,
                'email'   => $email,
                'message' => $message,
            ];

            return $this->success($data, 'User support sent successfully.', 200);

        } catch (\Exception $e) {
            return $this->error(['error' => $e->getMessage()], ' failed', 500);
        }
    }

}
