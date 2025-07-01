<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Enums\PageEnum;
use App\Enums\SectionEnum;
use App\Helpers\Helper;
use App\Models\CMS;
use App\Models\CustomerReview;
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

        return Helper::jsonResponse(true, 'How to work', 200, $data);

    }



    public function home_section()
    {
        $data = [];

        $cmsItems = CMS::where('page', PageEnum::HOME)
                    ->where('status', 'active')
                    ->whereIn('section', [SectionEnum::HOME_BANNER, SectionEnum::HOME_BANNERS])
                    ->get();

        $data['home_banner']    = $cmsItems->where('section', SectionEnum::HOME_BANNER)->first();
        $data['home_banners']   = $cmsItems->where('section', SectionEnum::HOME_BANNERS)->values();
        // $data['common']         = CMS::where('page', PageEnum::COMMON)->where('status', 'active')->get();
        // $data['settings']       = Setting::first();

        return Helper::jsonResponse(true, 'Home section', 200, $data);

    }

    public function recipe_section()
    {
        $data = [];

        $cmsItems = CMS::where('page', PageEnum::HOME)
                    ->where('status', 'active')
                    ->whereIn('section', [SectionEnum::HOME_RECIPE_PAGE, SectionEnum::HOME_RECIPE_PAGES])
                    ->get();

        $data['recipe_section']    = $cmsItems->where('section', SectionEnum::HOME_RECIPE_PAGE)->first();
        $data['recipe_sections']   = $cmsItems->where('section', SectionEnum::HOME_RECIPE_PAGES)->values();
        // $data['common']         = CMS::where('page', PageEnum::COMMON)->where('status', 'active')->get();
        // $data['settings']       = Setting::first();

        return Helper::jsonResponse(true, 'Recipe Page section', 200, $data);

    }


    public function contact_section()
    {
        $data = [];

        $cmsItems = CMS::where('page', PageEnum::HOME)
                    ->where('status', 'active')
                    ->whereIn('section', [SectionEnum::HOME_CONTACT_US, SectionEnum::HOME_CONTACT_USS])
                    ->get();

        $data['contact_section']    = $cmsItems->where('section', SectionEnum::HOME_CONTACT_US)->first();
        $data['contact_sections']   = $cmsItems->where('section', SectionEnum::HOME_CONTACT_USS)->values();
        // $data['common']         = CMS::where('page', PageEnum::COMMON)->where('status', 'active')->get();
        // $data['settings']       = Setting::first();

        return Helper::jsonResponse(true, 'Contact Us Page section', 200, $data);

    }



    public function personalized()
    {
        $data = [];

        $cmsItems = CMS::where('page', PageEnum::HOME)
                    ->where('status', 'active')
                    ->whereIn('section', [SectionEnum::PERSONALIZED, SectionEnum::PERSONALIZEDS])
                    ->get();

        $data['personalized']    = $cmsItems->where('section', SectionEnum::PERSONALIZED)->first();
        $data['personalizeds']   = $cmsItems->where('section', SectionEnum::PERSONALIZEDS)->values();
        // $data['common']         = CMS::where('page', PageEnum::COMMON)->where('status', 'active')->get();
        // $data['settings']       = Setting::first();

        return Helper::jsonResponse(true, 'Home section', 200, $data);

    }


    public function review()
    {
       $reviews = CustomerReview::all();


        $data = $reviews->map(function ($review) {
            return [
                'id'   => $review->id,
                'customer_name'       => $review->customer_name,
                'customer_image'       => $review->customer_image ? url($review->customer_image) : null,
                'content'       => $review->content ? $review->content: null,
                'rating'       => $review->rating ? (int) $review->rating: null,

            ];
        });


        return Helper::jsonResponse(true, 'Customer review retrive ', 200, $data);

    }
}
