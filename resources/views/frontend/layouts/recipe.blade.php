{{-- filepath: resources/views/recipe/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recipe Card</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; }
        .ingredients, .instructions, .swap-guide { margin-bottom: 20px; }
        .swap-guide { background: #f6f6f6; padding: 10px; border-radius: 8px; }
        .family-member { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Recipe: {{ $recipe['name'] }}</h2>
        <img src="{{ $recipe['image'] }}" alt="Recipe Image" width="200">
    </div>
    <div class="ingredients">
        <h3>Ingredients</h3>
        <ul>
            @foreach($ingredients as $ingredient)
                <li>{{ $ingredient['name'] }}</li>
            @endforeach
        </ul>
    </div>
    <div class="instructions">
        <h3>Instructions</h3>
        <ol>
            @foreach($instructions as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>
    </div>
    <div class="swap-guide">
        <h3>Swap Guide for Each Family Member</h3>
        @foreach($swap_guide as $swap)
            <div class="family-member">{{ $swap['family_member']['name'] }}</div>
            <ul>
                @foreach($swap['ingredients'] as $ingredient)
                    <li>{{ $ingredient['name'] }}</li>
                @endforeach
            </ul>
        @endforeach
    </div>
</body>
</html>