<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\IngredientSection;
use App\Models\RecipeInstruction;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $recipe = Recipe::create([
            'title' => 'Prawns with Cauli Mash',
            'short_description' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Possimus, facere adipisci nostrum placeat culpa et ipsum magnam delectus soluta natus',
            'image_url' => 'https://live-production.wcms.abc-cdn.net.au/3daf358784e3e82e0ec532e944091186?impolicy=wcms_crop_resize&cropH=2000&cropW=3000&xPos=0&yPos=0&width=862&height=575'
        ]);

        // Ingredient Section 1: Prep
        $prep = $recipe->ingredientSections()->create([
            'title' => '1 Prep',
            'order' => 1,
        ]);

        $prep->ingredients()->createMany([
            ['name' => 'Prawns 7*', 'amount' => '350 grams', 'is_highlighted' => true],
            ['name' => 'Shallots', 'amount' => '1 Piece'],
            ['name' => 'Snow peas', 'amount' => '100 Grams', 'is_highlighted' => true],
            ['name' => 'Vegetable oil', 'amount' => '1 Tbsp'],
        ]);

        // Ingredient Section 2: Mash
        $mash = $recipe->ingredientSections()->create([
            'title' => 'Cauli bean mash',
            'order' => 2,
        ]);

        $mash->ingredients()->createMany([
            ['name' => 'Cauliflower', 'amount' => '400 grams', 'is_highlighted' => true],
            ['name' => 'White beans', 'amount' => '240 Grams'],
            ['name' => 'Potato Seasoning', 'amount' => '4 grams', 'is_highlighted' => true],
        ]);

        // Ingredient Section 3: Serve
        $serve = $recipe->ingredientSections()->create([
            'title' => 'To serve',
            'order' => 3,
        ]);

        $serve->ingredients()->createMany([
            ['name' => 'Fresh coriander', 'amount' => '15 Grams', 'is_highlighted' => true],
            ['name' => 'Lime', 'amount' => '1 Piece'],
        ]);

        // Instructions
        $recipe->instructions()->createMany([
            [
                'step_number' => 1,
                'title' => 'Prepare the Ingredients',
                'description' => 'Peel and devein the prawns. Thinly slice shallots and trim the snow peas.',
                'image_url' => null,
            ],
            [
                'step_number' => 2,
                'title' => 'Make the Cauli Mash',
                'description' => 'Steam cauliflower and blend with white beans and seasoning until smooth.',
                'image_url' => null,
            ],
            [
                'step_number' => 3,
                'title' => 'Cook the Prawns',
                'description' => 'Heat oil and cook prawns with shallots until just done. Add snow peas and cook for 1 more minute.',
                'image_url' => null,
            ],
            [
                'step_number' => 4,
                'title' => 'Serve',
                'description' => 'Plate mash, top with prawns and peas. Garnish with coriander and lime wedges.',
                'image_url' => null,
            ],
        ]);
    }
}
