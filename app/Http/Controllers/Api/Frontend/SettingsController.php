<?php
namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Http\Controllers\Controller;

class SettingsController extends Controller{
    public function index(){
        $settings = Setting::first();
        $social_links = SocialLink::where('status', 'active')->get();
        $data = [
            'settings' => $settings,
            'social_links' => $social_links
        ];
        return Helper::jsonResponse(true, 'About Page', 200, $data);
    }
}
