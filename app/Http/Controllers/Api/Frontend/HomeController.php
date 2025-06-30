<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Helpers\Helper;
use App\Models\CMS;
use App\Models\Setting;

class HomeController extends Controller
{
    public function how_work()
    {
        $data = [];

        $cmsItems = CMS::where('page', PageEnum::HOME)
                    ->where('status', 'active')
                    ->whereIn('section', [SectionEnum::HOME_HOW_IT_WORK, SectionEnum::HOME_HOW_IT_WORKS])
                    ->get();

        $data['how_it_work']    = $cmsItems->where('section', SectionEnum::HOME_HOW_IT_WORK)->first();
        $data['how_it_works']   = $cmsItems->where('section', SectionEnum::HOME_HOW_IT_WORKS)->values();
        // $data['common']         = CMS::where('page', PageEnum::COMMON)->where('status', 'active')->get();
        // $data['settings']       = Setting::first();

        return Helper::jsonResponse(true, 'Home Page', 200, $data);

    }
}
