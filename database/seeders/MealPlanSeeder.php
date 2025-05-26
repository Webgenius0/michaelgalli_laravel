<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MealPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'Plan for 1 Person - 3 Recipes',
                'people' => 1,
                'recipes_per_week' => 3,
                'price_per_serving' => 50,  // AED per serving
                'stripe_price_id' => 'price_1Person_3Recipes', // your Stripe price id here
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plan for 2 People - 4 Recipes',
                'people' => 2,
                'recipes_per_week' => 4,
                'price_per_serving' => 48,
                'stripe_price_id' => 'price_2People_4Recipes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plan for 3 People - 5 Recipes',
                'people' => 3,
                'recipes_per_week' => 5,
                'price_per_serving' => 47,
                'stripe_price_id' => 'price_3People_5Recipes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plan for 4 People - 5 Recipes',
                'people' => 4,
                'recipes_per_week' => 5,
                'price_per_serving' => 46,
                'stripe_price_id' => 'price_4People_5Recipes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('meal_plans')->insert($plans);
    }
}
