{{-- filepath: resources/views/recipe/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recipe Card - {{ $recipe['name'] }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        .recipe-card {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 1020px;
        }
        
        .left-panel {
            background: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
        }
        
        .right-panel {
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
            color: white;
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }
        
        .right-panel::before {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M20,20 Q50,5 80,20 Q95,50 80,80 Q50,95 20,80 Q5,50 20,20" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: contain;
            background-repeat: no-repeat;
        }
        
        .recipe-image {
            width: 100%;
            max-width: 250px;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .recipe-title {
            font-size: 28px;
            font-weight: bold;
            color: #2E7D32;
            margin: 0 0 8px 0;
            line-height: 1.2;
        }
        
        .recipe-subtitle {
            color: #4CAF50;
            font-size: 16px;
            margin: 0 0 30px 0;
            font-weight: 500;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 20px 0;
            color: #2E7D32;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 8px;
        }
        
        .white-section-title {
            color: white;
            border-bottom-color: rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }
        
        .ingredients-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .ingredients-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
            padding-left: 20px;
            color: #333;
            font-size: 14px;
        }
        
        .ingredients-list li:before {
            content: '•';
            color: #4CAF50;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 16px;
        }
        
        .ingredients-list li:last-child {
            border-bottom: none;
        }
        
        .instructions-list {
            list-style: none;
            padding: 0;
            margin: 0;
            counter-reset: step-counter;
            position: relative;
            z-index: 2;
        }
        
        .instructions-list li {
            counter-increment: step-counter;
            padding: 12px 0;
            position: relative;
            padding-left: 35px;
            color: white;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .instructions-list li:before {
            content: counter(step-counter);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            position: absolute;
            left: 0;
            top: 12px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .swap-guide-section {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, #388E3C 0%, #1B5E20 100%);
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .swap-guide-section::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }
        
        .swap-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 30px 0;
            position: relative;
            z-index: 2;
        }
        
        .family-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            position: relative;
            z-index: 2;
        }
        
        .family-member-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .family-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 15px 0;
            color: white;
            text-align: center;
        }
        
        .family-swaps {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .family-swaps li {
            padding: 6px 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            position: relative;
            padding-left: 15px;
        }
        
        .family-swaps li:before {
            content: '→';
            position: absolute;
            left: 0;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Print optimizations */
        @media print {
            body {
                background: white;
            }
            
            .recipe-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .right-panel,
            .swap-guide-section {
                background: #4CAF50 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
        
        /* Single column layout for narrow screens */
        @media (max-width: 600px) {
            .recipe-card {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="recipe-card">
        <!-- Left Panel - Ingredients -->
        <div class="left-panel">
            @if(isset($recipe['image']) && $recipe['image'])
                <img src="{{ asset($recipe['image'] ) }}" alt="{{ $recipe['name'] }}" class="recipe-image">
            @endif
            
            <h1 class="recipe-title">{{ $recipe['name'] }}</h1>
            <p class="recipe-subtitle">Delicious homemade recipe</p>
            
            <h2 class="section-title">Ingredients</h2>
            <ul class="ingredients-list">
                @foreach($ingredients as $ingredient)
                    <li>{{ $ingredient['name'] }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Right Panel - Instructions -->
        <div class="right-panel">
            <h2 class="section-title white-section-title">Instructions</h2>
            <ol class="instructions-list">
                @foreach($instructions as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        </div>
    </div>

    <!-- Swap Guide Section -->
    @if(isset($swap_guide) && count($swap_guide) > 0)
    <div class="recipe-card" style="margin-top: 20px;">
        <div class="swap-guide-section">
            <h2 class="swap-title">Swap Guide for Each Family Member</h2>
            <div class="family-grid">
                @foreach($swap_guide as $swap)
                    <div class="family-member-card">
                        <h3 class="family-name">{{ $swap['family_member']['name'] }}</h3>
                        <ul class="family-swaps">
                            @foreach($swap['ingredients'] as $ingredient)
                                <li>{{ $ingredient['name'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</body>
</html>