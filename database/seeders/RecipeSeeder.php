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
        // Existing four recipes
        $this->createRecipe(
            title: 'Prawns with Cauli Mash',
            shortDescription: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            imageUrl: 'https://live-production.wcms.abc-cdn.net.au/3daf358784e3e82e0ec532e944091186?impolicy=wcms_crop_resize&cropH=2000&cropW=3000&xPos=0&yPos=0&width=862&height=575',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Prawns 7*', 'amount' => '350 grams', 'is_highlighted' => true],
                        ['name' => 'Shallots', 'amount' => '1 Piece'],
                        ['name' => 'Snow peas', 'amount' => '100 Grams', 'is_highlighted' => true],
                        ['name' => 'Vegetable oil', 'amount' => '1 Tbsp'],
                    ],
                ],
                [
                    'title' => 'Cauli bean mash',
                    'order' => 2,
                    'ingredients' => [
                        ['name' => 'Cauliflower', 'amount' => '400 grams', 'is_highlighted' => true],
                        ['name' => 'White beans', 'amount' => '240 Grams'],
                        ['name' => 'Potato Seasoning', 'amount' => '4 grams', 'is_highlighted' => true],
                    ],
                ],
                [
                    'title' => 'To serve',
                    'order' => 3,
                    'ingredients' => [
                        ['name' => 'Fresh coriander', 'amount' => '15 Grams', 'is_highlighted' => true],
                        ['name' => 'Lime', 'amount' => '1 Piece'],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Prepare the Ingredients', 'description' => 'Peel and devein the prawns. Thinly slice shallots and trim the snow peas.'],
                ['step_number' => 2, 'title' => 'Make the Cauli Mash', 'description' => 'Steam cauliflower and blend with white beans and seasoning until smooth.'],
                ['step_number' => 3, 'title' => 'Cook the Prawns', 'description' => 'Heat oil and cook prawns with shallots until just done. Add snow peas and cook for 1 more minute.'],
                ['step_number' => 4, 'title' => 'Serve', 'description' => 'Plate mash, top with prawns and peas. Garnish with coriander and lime wedges.'],
            ]
        );

        $this->createRecipe(
            title: 'Grilled Chicken with Quinoa Salad',
            shortDescription: 'A healthy and balanced meal packed with protein and fiber.',
            imageUrl: 'https://images.unsplash.com/photo-1604908554163-275b1c28c707?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Chicken breast', 'amount' => '400 grams', 'is_highlighted' => true],
                        ['name' => 'Lemon', 'amount' => '1 Piece'],
                        ['name' => 'Olive oil', 'amount' => '2 Tbsp'],
                    ],
                ],
                [
                    'title' => 'Quinoa Salad',
                    'order' => 2,
                    'ingredients' => [
                        ['name' => 'Quinoa', 'amount' => '200 grams'],
                        ['name' => 'Cherry tomatoes', 'amount' => '100 grams'],
                        ['name' => 'Cucumber', 'amount' => '1 Piece'],
                        ['name' => 'Feta cheese', 'amount' => '50 grams', 'is_highlighted' => true],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Marinate Chicken', 'description' => 'Marinate chicken with lemon juice, olive oil, salt, and pepper.'],
                ['step_number' => 2, 'title' => 'Cook Quinoa', 'description' => 'Boil quinoa until fluffy. Let it cool.'],
                ['step_number' => 3, 'title' => 'Grill Chicken', 'description' => 'Grill chicken on medium-high heat until fully cooked.'],
                ['step_number' => 4, 'title' => 'Assemble Salad', 'description' => 'Mix quinoa, vegetables, and feta. Serve with sliced grilled chicken.'],
            ]
        );

        $this->createRecipe(
            title: 'Creamy Mushroom Pasta',
            shortDescription: 'A rich and creamy pasta dish packed with mushrooms and herbs.',
            imageUrl: 'https://images.unsplash.com/photo-1601924582975-4a3b87b33080?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Mushrooms', 'amount' => '300 grams', 'is_highlighted' => true],
                        ['name' => 'Garlic', 'amount' => '2 Cloves'],
                        ['name' => 'Onion', 'amount' => '1 Small'],
                    ],
                ],
                [
                    'title' => 'Sauce',
                    'order' => 2,
                    'ingredients' => [
                        ['name' => 'Cooking cream', 'amount' => '200 ml', 'is_highlighted' => true],
                        ['name' => 'Vegetable broth', 'amount' => '100 ml'],
                        ['name' => 'Butter', 'amount' => '1 Tbsp'],
                    ],
                ],
                [
                    'title' => 'Pasta',
                    'order' => 3,
                    'ingredients' => [
                        ['name' => 'Fettuccine pasta', 'amount' => '250 grams'],
                        ['name' => 'Parmesan cheese', 'amount' => '30 grams', 'is_highlighted' => true],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Boil Pasta', 'description' => 'Cook the pasta in salted water until al dente. Drain and set aside.'],
                ['step_number' => 2, 'title' => 'Sauté Veggies', 'description' => 'Sauté garlic, onion, and mushrooms in butter until golden.'],
                ['step_number' => 3, 'title' => 'Make Sauce', 'description' => 'Add cream and broth to the pan. Simmer for 5 minutes.'],
                ['step_number' => 4, 'title' => 'Combine and Serve', 'description' => 'Mix pasta with sauce. Top with parmesan before serving.'],
            ]
        );

        $this->createRecipe(
            title: 'Thai Green Curry',
            shortDescription: 'A flavorful Thai curry made with coconut milk and green curry paste.',
            imageUrl: 'https://images.unsplash.com/photo-1585238342029-5e6e8121e7de?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Chicken thigh', 'amount' => '400 grams', 'is_highlighted' => true],
                        ['name' => 'Eggplant', 'amount' => '1 Small'],
                        ['name' => 'Green beans', 'amount' => '100 grams'],
                    ],
                ],
                [
                    'title' => 'Curry Base',
                    'order' => 2,
                    'ingredients' => [
                        ['name' => 'Green curry paste', 'amount' => '3 Tbsp', 'is_highlighted' => true],
                        ['name' => 'Coconut milk', 'amount' => '400 ml', 'is_highlighted' => true],
                        ['name' => 'Fish sauce', 'amount' => '1 Tbsp'],
                    ],
                ],
                [
                    'title' => 'To Serve',
                    'order' => 3,
                    'ingredients' => [
                        ['name' => 'Jasmine rice', 'amount' => '2 Cups'],
                        ['name' => 'Thai basil', 'amount' => 'A handful'],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Cook Curry Paste', 'description' => 'Fry curry paste in oil until fragrant.'],
                ['step_number' => 2, 'title' => 'Add Chicken and Veggies', 'description' => 'Add chicken, eggplant, and beans. Cook for 5–6 minutes.'],
                ['step_number' => 3, 'title' => 'Simmer with Coconut Milk', 'description' => 'Add coconut milk and simmer until chicken is cooked.'],
                ['step_number' => 4, 'title' => 'Serve', 'description' => 'Serve hot with rice and garnish with Thai basil.'],
            ]
        );

        // New 6 recipes
        $this->createRecipe(
            title: 'Beef Stir‑Fry with Broccoli',
            shortDescription: 'Quick and flavorful beef stir‑fry with crisp-tender broccoli.',
            imageUrl: 'https://images.unsplash.com/photo-1589308078056-f825b9b47cc4?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Beef sirloin', 'amount' => '400 grams', 'is_highlighted' => true],
                        ['name' => 'Broccoli florets', 'amount' => '300 grams', 'is_highlighted' => true],
                        ['name' => 'Garlic', 'amount' => '3 cloves'],
                        ['name' => 'Soy sauce', 'amount' => '3 Tbsp', 'is_highlighted' => true],
                        ['name' => 'Sesame oil', 'amount' => '1 Tbsp'],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Marinate Beef', 'description' => 'Slice beef thinly and marinate with soy sauce and garlic for 10 minutes.'],
                ['step_number' => 2, 'title' => 'Cook Broccoli', 'description' => 'Blanch broccoli in boiling water for 2 minutes, then drain.'],
                ['step_number' => 3, 'title' => 'Stir‑Fry', 'description' => 'Heat sesame oil, stir fry beef until nearly cooked, add broccoli and toss together 2 minutes.'],
                ['step_number' => 4, 'title' => 'Serve', 'description' => 'Plate immediately over rice or noodles.'],
            ]
        );

        $this->createRecipe(
            title: 'Salmon with Asparagus & Lemon Butter',
            shortDescription: 'Oven‑baked salmon with tender asparagus and zesty lemon butter.',
            imageUrl: 'https://images.unsplash.com/photo-1514516875663-0e06eff7b808?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Salmon fillets', 'amount' => '2 × 200g', 'is_highlighted' => true],
                        ['name' => 'Asparagus', 'amount' => '250 grams', 'is_highlighted' => true],
                        ['name' => 'Butter', 'amount' => '2 Tbsp', 'is_highlighted' => true],
                        ['name' => 'Lemon', 'amount' => '1 Piece'],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Prepare Salmon', 'description' => 'Preheat oven to 200°C. Place salmon and asparagus on a tray, season with salt and pepper.'],
                ['step_number' => 2, 'title' => 'Make Lemon Butter', 'description' => 'Melt butter, mix in lemon juice and zest.'],
                ['step_number' => 3, 'title' => 'Bake', 'description' => 'Drizzle lemon butter over salmon and asparagus. Bake 12–15 minutes until salmon is done.'],
            ]
        );

        $this->createRecipe(
            title: 'Veggie Tacos with Black Bean Salsa',
            shortDescription: 'Colorful tacos filled with roasted veggies and a zesty black bean salsa.',
            imageUrl: 'https://images.unsplash.com/photo-1601924578312-2c489193415d?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Corn tortillas', 'amount' => '8 pieces'],
                        ['name' => 'Bell peppers', 'amount' => '2 mixed', 'is_highlighted' => true],
                        ['name' => 'Zucchini', 'amount' => '1 large', 'is_highlighted' => true],
                        ['name' => 'Cumin', 'amount' => '1 tsp'],
                    ],
                ],
                [
                    'title' => 'Black Bean Salsa',
                    'order' => 2,
                    'ingredients' => [
                        ['name' => 'Black beans', 'amount' => '400 grams', 'is_highlighted' => true],
                        ['name' => 'Red onion', 'amount' => '½ Piece'],
                        ['name' => 'Cilantro', 'amount' => 'handful'],
                        ['name' => 'Lime', 'amount' => '1 Piece', 'is_highlighted' => true],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Roast Veggies', 'description' => 'Slice peppers and zucchini, toss with oil, cumin, salt, roast 15 minutes at 200°C.'],
                ['step_number' => 2, 'title' => 'Make Salsa', 'description' => 'Combine beans, diced onion, cilantro and lime juice.'],
                ['step_number' => 3, 'title' => 'Warm Tortillas', 'description' => 'Warm tortillas in a dry skillet.'],
                ['step_number' => 4, 'title' => 'Assemble Tacos', 'description' => 'Fill tacos with roasted veggies, top with black bean salsa.'],
            ]
        );

        $this->createRecipe(
            title: 'Shrimp & Avocado Salad',
            shortDescription: 'Light and refreshing shrimp salad perfect for warm days.',
            imageUrl: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Cooked shrimp', 'amount' => '300 grams', 'is_highlighted' => true],
                        ['name' => 'Avocado', 'amount' => '2 Pieces', 'is_highlighted' => true],
                        ['name' => 'Cherry tomatoes', 'amount' => '150 grams'],
                        ['name' => 'Mixed greens', 'amount' => '100 grams'],
                    ],
                ],
                [
                    'title' => 'Dressing',
                    'order' => 2,
                    'ingredients' => [
                        ['name' => 'Olive oil', 'amount' => '3 Tbsp'],
                        ['name' => 'Lime juice', 'amount' => '2 Tbsp', 'is_highlighted' => true],
                        ['name' => 'Honey', 'amount' => '1 tsp'],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Prepare Salad', 'description' => 'Slice avocado, halve tomatoes, combine with shrimp and greens.'],
                ['step_number' => 2, 'title' => 'Make Dressing', 'description' => 'Whisk olive oil, lime juice, honey, salt and pepper.'],
                ['step_number' => 3, 'title' => 'Toss and Serve', 'description' => 'Pour dressing over salad and toss gently.'],
            ]
        );

        $this->createRecipe(
            title: 'Pork Tenderloin with Apple Chutney',
            shortDescription: 'Juicy pork tenderloin served with a sweet and tangy apple chutney.',
            imageUrl: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Pork tenderloin', 'amount' => '500 grams', 'is_highlighted' => true],
                        ['name' => 'Apple', 'amount' => '2 Pieces', 'is_highlighted' => true],
                        ['name' => 'Onion', 'amount' => '1 Piece'],
                        ['name' => 'Apple cider vinegar', 'amount' => '2 Tbsp', 'is_highlighted' => true],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Cook Pork', 'description' => 'Season tenderloin, sear all sides in a hot pan, then finish in oven at 180°C for 15 mins.'],
                ['step_number' => 2, 'title' => 'Make Chutney', 'description' => 'Dice apples and onion, simmer with vinegar, sugar and spice until soft.'],
                ['step_number' => 3, 'title' => 'Serve', 'description' => 'Slice pork and spoon apple chutney over top.'],
            ]
        );

        $this->createRecipe(
            title: 'Vegetable Risotto',
            shortDescription: 'Creamy risotto loaded with seasonal vegetables and Parmesan.',
            imageUrl: 'https://images.unsplash.com/photo-1523986371872-9d3ba2e2f4b?auto=format&fit=crop&w=800&q=80',
            sections: [
                [
                    'title' => '1 Prep',
                    'order' => 1,
                    'ingredients' => [
                        ['name' => 'Arborio rice', 'amount' => '300 grams', 'is_highlighted' => true],
                        ['name' => 'Vegetable broth', 'amount' => '1 liter'],
                        ['name' => 'Zucchini', 'amount' => '1 Piece'],
                        ['name' => 'Peas', 'amount' => '100 grams'],
                        ['name' => 'Parmesan cheese', 'amount' => '50 grams', 'is_highlighted' => true],
                    ],
                ],
            ],
            instructions: [
                ['step_number' => 1, 'title' => 'Sauté Veggies', 'description' => 'Sauté zucchini in olive oil until soft.'],
                ['step_number' => 2, 'title' => 'Cook Rice', 'description' => 'Add rice, toast 2 mins. Gradually ladle broth, stirring often until creamy.'],
                ['step_number' => 3, 'title' => 'Finish and Serve', 'description' => 'Stir in peas and Parmesan, season, then serve warm.'],
            ]
        );
    }

    private function createRecipe(string $title, string $shortDescription, string $imageUrl, array $sections, array $instructions)
    {
        $recipe = Recipe::create([
            'title' => $title,
            'short_description' => $shortDescription,
            'image_url' => $imageUrl,
        ]);

        foreach ($sections as $section) {
            $ingredientSection = $recipe->ingredientSections()->create([
                'title' => $section['title'],
                'order' => $section['order'],
            ]);
            $ingredientSection->ingredients()->createMany($section['ingredients']);
        }

        $recipe->instructions()->createMany($instructions);
    }
}
