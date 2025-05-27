<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MealPlan;

class MealPlanSeeder extends Seeder
{
    public function run()
    {
        MealPlan::create([
            'name' => '2 People, 3 Recipes',
            'people' => 2,
            'recipes_per_week' => 3,
            'price_per_serving' => 48.00,
            'stripe_price_id' => 'price_1RTGJpB1kC1m7lm1BudbhcaH', // Replace with real ID
        ]);

        MealPlan::create([
            'name' => '2 People, 5 Recipes',
            'people' => 2,
            'recipes_per_week' => 5,
            'price_per_serving' => 45.00,
            'stripe_price_id' => 'price_1RTGKPB1kC1m7lm1WW7vjOfz', // Replace with real ID
        ]);

        MealPlan::create([
            'name' => '4 People, 3 Recipes',
            'people' => 4,
            'recipes_per_week' => 3,
            'price_per_serving' => 44.00,
            'stripe_price_id' => 'price_1RTGL3B1kC1m7lm1YWcgcCPx', // Replace with real ID
        ]);

        MealPlan::create([
            'name' => '4 People, 5 Recipes',
            'people' => 4,
            'recipes_per_week' => 5,
            'price_per_serving' => 42.00,
            'stripe_price_id' => 'price_1RTGMCB1kC1m7lm1CSuz5LgA', // Replace with real ID
        ]);
    }
}
