<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Carb;
use App\Models\Cuisine;
use App\Models\Protein;
use App\Models\Calories;
use App\Models\Category;
use App\Models\HealthGoal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TimeToClock;

class RecipeFilterController extends Controller
{
    use ApiResponse;

    public function category_list(Request $request)
    {
        $categories = Category::select('id', 'name', 'slug', 'image')
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        if ($categories->isEmpty()) {
            return $this->error([], 'No categories Found', 404);
        }

        return $this->success($categories, 'Category List Retrieved Successfully');
    }

    public function protein_list(Request $request)
    {
        $proteins = Protein::select('id', 'name')

            ->orderBy('name', 'asc')
            ->get();

        if ($proteins->isEmpty()) {
            return $this->error([], 'No proteins Found', 404);
        }

        return $this->success($proteins, 'Protein List Retrieved Successfully');
    }

    public function calories_list(Request $request)
    {
        $calories  = Calories::select('id', 'name')

            ->orderBy('name', 'asc')
            ->get();

        if ($calories->isEmpty()) {
            return $this->error([], 'No proteins Found', 404);
        }

        return $this->success($calories, 'Calories List Retrieved Successfully');
    }

    public function carbs_list(Request $request)
    {
        $carbs = Carb::select('id', 'name')

            ->orderBy('name', 'asc')
            ->get();

        if ($carbs->isEmpty()) {
            return $this->error([], 'No carbs Found', 404);
        }

        return $this->success($carbs, 'Carbs List Retrieved Successfully');
    }

    public function cuisine_list(Request $request)
    {
        $cuisines = Cuisine::select('id', 'name')

            ->orderBy('name', 'asc')
            ->get();

        if ($cuisines->isEmpty()) {
            return $this->error([], 'No cuisines Found', 404);
        }

        return $this->success($cuisines, 'Cuisine List Retrieved Successfully');
    }

    public function health_goal_list(Request $request)
    {
        $health_goals = HealthGoal::select('id', 'name')

            ->orderBy('name', 'asc')
            ->get();

        if ($health_goals->isEmpty()) {
            return $this->error([], 'No health goals Found', 404);
        }

        return $this->success($health_goals, 'Health Goal List Retrieved Successfully');
    }

    public function time_to_cook_list(Request $request)
    {
        $health_goals = TimeToClock::select('id', 'name')

            ->orderBy('name', 'asc')
            ->get();

        return $this->success($health_goals, 'Time to Cook List Retrieved Successfully');
    }
}
