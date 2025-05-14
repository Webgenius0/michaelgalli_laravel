@extends('backend.app', ['title' => 'Create Recipe'])

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Create Recipe</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Recipe</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </div>
                </div>

                <div class="row" id="user-profile">
                    <div class="col-lg-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Create Recipe</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body border-0">
                                <form class="form-horizontal" method="POST" action="{{ route('admin.recipe.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Recipe Title -->
                                    <div class="form-group">
                                        <label for="title">Recipe Title</label>
                                        <input type="text" name="title" class="form-control"
                                            value="{{ old('title') }}" required>
                                    </div>

                                    <!-- Short Description -->
                                    <div class="form-group">
                                        <label for="short_description">Short Description</label>
                                        <textarea name="short_description" class="form-control" rows="3">{{ old('short_description') }}</textarea>
                                    </div>

                                    <!-- Long Description -->
                                    <div class="form-group">
                                        <label for="long_description">Long Description</label>
                                        <textarea name="long_description" class="form-control" rows="5">{{ old('long_description') }}</textarea>
                                    </div>

                                    <!-- Recipe Image URL -->
                                    <div class="form-group">
                                        
                                        <label for="image_url" class="form-label">Thumbnail:</label>
                                        <input type="file"
                                            class="dropify form-control @error('image_url') is-invalid @enderror"
                                            data-default-file="" name="image_url"
                                            id="image_url">
                                        @error('image_url')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>



                                    <!-- Categories (Protein, Calories, Carbs, etc.) -->




                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="protein_id">Protein</label>
                                                <input type="text" name="protein_id" class="form-control"
                                                    value="{{ old('protein_id') }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="calories_id">Calories</label>
                                                <input type="text" name="calories_id" class="form-control"
                                                    value="{{ old('calories_id') }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="carb_id">Carbs</label>
                                                <input type="text" name="carb_id" class="form-control"
                                                    value="{{ old('carb_id') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cuisine_id">Cuisine</label>
                                                <input type="text" name="cuisine_id" class="form-control"
                                                    value="{{ old('cuisine_id') }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="health_goal_id">Health Goal</label>
                                                <input type="text" name="health_goal_id" class="form-control"
                                                    value="{{ old('health_goal_id') }}">
                                            </div>

                                            <div class="form-group">
                                                <label for="time_to_clock_id">Time to Clock</label>
                                                <input type="text" name="time_to_clock_id" class="form-control"
                                                    value="{{ old('time_to_clock_id') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category and Subcategory -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category_id" class="form-label">Category:</label>
                                                <select class="form-control @error('category_id') is-invalid @enderror"
                                                    name="category_id" id="category_id">
                                                    <option>Select a Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="subcategory_id" class="form-label">Subcategory:</label>
                                                <select class="form-control @error('subcategory_id') is-invalid @enderror"
                                                    name="subcategory_id" id="subcategory_id">
                                                    <option>Select a Subcategory</option>
                                                </select>
                                                @error('subcategory_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Ingredients Section -->
                                    <div id="ingredients-wrapper">
                                        <h3>Ingredients</h3>
                                        <div class="ingredient-category-group">
                                            <!-- Ingredient Category -->
                                            <div class="form-group">
                                                <label for="category_name">Ingredient Category</label>
                                                <input type="text" name="ingredient_categories[0][category_name]"
                                                    class="form-control"
                                                    placeholder="Category (e.g., 1 Prep, Cauli bean mash)" required>
                                            </div>

                                            <!-- Ingredients (Dynamic Input) -->
                                            <div class="ingredient-group">
                                                <div class="form-group">
                                                    <label for="ingredient_name">Ingredient Name</label>
                                                    <input type="text"
                                                        name="ingredient_categories[0][ingredients][0][ingredient_name]"
                                                        class="form-control" placeholder="Ingredient Name (e.g., Prawns)"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="quantity">Quantity</label>
                                                    <input type="text"
                                                        name="ingredient_categories[0][ingredients][0][quantity]"
                                                        class="form-control" placeholder="Quantity (e.g., 350 grams)"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="unit">Unit</label>
                                                    <input type="text"
                                                        name="ingredient_categories[0][ingredients][0][unit]"
                                                        class="form-control" placeholder="Unit (e.g., grams, piece, tbsp)"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" id="add-category" class="btn btn-secondary">Add
                                        Category</button>
                                    <button type="button" id="add-ingredient" class="btn btn-secondary">Add
                                        Ingredient</button>

                                    <!-- Instructions Section -->
                                    <!-- Instructions Section -->
                                    <div id="instructions-wrapper">
                                        <h3>Instructions</h3>
                                        <div class="instruction-step">
                                            <div class="form-group">
                                                <label for="title">Instruction Title</label>
                                                <input type="text" name="instructions[0][title]" class="form-control"
                                                    placeholder="Step Title (e.g., 'Step 1: Prepare Ingredients')"
                                                    required>
                                            </div>

                                            <div class="form-group">
                                                <label for="step_number">Step Number</label>
                                                <input type="number" name="instructions[0][step_number]"
                                                    class="form-control" placeholder="Step Number" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="instructions[0][description]" class="form-control" placeholder="Instruction Description" required></textarea>
                                            </div>

                                            <!-- Image Upload for Instruction -->
                                            <div class="form-group">
                                                <label for="instruction_image">Step Image (Optional)</label>
                                                <input type="file" name="instructions[0][image_url]"
                                                    class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" id="add-instruction" class="btn btn-secondary">Add
                                        Instruction</button>


                                    <!-- Submit Button -->
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">Submit Recipe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
    <script>
        // Add category dynamically
        document.getElementById('add-category').addEventListener('click', function() {
            var categoryGroup = document.createElement('div');
            categoryGroup.classList.add('ingredient-category-group');
            categoryGroup.innerHTML = `
        <div class="form-group">
            <label for="category_name">Ingredient Category</label>
            <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length}][category_name]" class="form-control" placeholder="Category (e.g., Cauli bean mash)" required>
        </div>
        <div class="ingredient-group">
            <div class="form-group">
                <label for="ingredient_name">Ingredient Name</label>
                <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length}][ingredients][0][ingredient_name]" class="form-control" placeholder="Ingredient Name (e.g., Cauliflower)" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length}][ingredients][0][quantity]" class="form-control" placeholder="Quantity (e.g., 400 grams)" required>
            </div>
            <div class="form-group">
                <label for="unit">Unit</label>
                <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length}][ingredients][0][unit]" class="form-control" placeholder="Unit (e.g., grams, tbsp)" required>
            </div>
        </div>
        <button type="button" class="btn btn-danger remove-category">Remove Category</button>
    `;
            document.getElementById('ingredients-wrapper').appendChild(categoryGroup);

            // Add remove functionality for category
            categoryGroup.querySelector('.remove-category').addEventListener('click', function() {
                categoryGroup.remove();
            });
        });

        // Add ingredient dynamically
        document.getElementById('add-ingredient').addEventListener('click', function() {
            var ingredientGroup = document.createElement('div');
            ingredientGroup.classList.add('ingredient-group');
            ingredientGroup.innerHTML = `
        <div class="form-group">
            <label for="ingredient_name">Ingredient Name</label>
            <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length - 1}][ingredients][${document.querySelectorAll('.ingredient-group').length}][ingredient_name]" class="form-control" placeholder="Ingredient Name (e.g., Prawns)" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length - 1}][ingredients][${document.querySelectorAll('.ingredient-group').length}][quantity]" class="form-control" placeholder="Quantity (e.g., 350 grams)" required>
        </div>
        <div class="form-group">
            <label for="unit">Unit</label>
            <input type="text" name="ingredient_categories[${document.querySelectorAll('.ingredient-category-group').length - 1}][ingredients][${document.querySelectorAll('.ingredient-group').length}][unit]" class="form-control" placeholder="Unit (e.g., grams, piece, tbsp)" required>
        </div>
        <button type="button" class="btn btn-danger remove-ingredient">Remove Ingredient</button>
    `;
            document.querySelector('.ingredient-category-group:last-child').appendChild(ingredientGroup);

            // Add remove functionality for ingredient
            ingredientGroup.querySelector('.remove-ingredient').addEventListener('click', function() {
                ingredientGroup.remove();
            });
        });

        // Add instruction step dynamically
        document.getElementById('add-instruction').addEventListener('click', function() {
            var instructionStep = document.createElement('div');
            instructionStep.classList.add('instruction-step');
            instructionStep.innerHTML = `
        <div class="form-group">
            <label for="title">Instruction Title</label>
            <input type="text" name="instructions[${document.querySelectorAll('.instruction-step').length}][title]" class="form-control" placeholder="Step Title (e.g., 'Step 1: Prepare Ingredients')" required>
        </div>
        <div class="form-group">
            <label for="step_number">Step Number</label>
            <input type="number" name="instructions[${document.querySelectorAll('.instruction-step').length}][step_number]" class="form-control" placeholder="Step Number" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="instructions[${document.querySelectorAll('.instruction-step').length}][description]" class="form-control" placeholder="Instruction Description" required></textarea>
        </div>
        <div class="form-group">
            <label for="instruction_image">Step Image (Optional)</label>
            <input type="file" name="instructions[${document.querySelectorAll('.instruction-step').length}][image_url]" class="form-control" accept="image/*">
        </div>
        <button type="button" class="btn btn-danger remove-instruction">Remove Instruction</button>
    `;
            document.getElementById('instructions-wrapper').appendChild(instructionStep);

            // Add remove functionality for instruction
            instructionStep.querySelector('.remove-instruction').addEventListener('click', function() {
                instructionStep.remove();
            });
        });
    </script>
@endpush
