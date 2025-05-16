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
                                        <input type="text" name="title" placeholder="Enter title" class="form-control"
                                            value="{{ old('title') }}" required>
                                    </div>

                                    <!-- Short Description -->
                                    <div class="form-group">
                                        <label for="short_description">Short Description</label>
                                        <textarea name="short_description" placeholder="Enter description" class="form-control" rows="3">{{ old('short_description') }}</textarea>
                                    </div>

                                    <!-- Long Description -->
                                    <div class="form-group">
                                        <label for="long_description">Long Description</label>
                                        <textarea name="long_description" class="form-control" placeholder="Enter description" rows="5">{{ old('long_description') }}</textarea>
                                    </div>

                                    <!-- Recipe Image URL -->
                                    <div class="form-group">
                                        <label for="image_url" class="form-label">Thumbnail:</label>
                                        <input type="file"
                                            class="dropify form-control @error('image_url') is-invalid @enderror"
                                            data-default-file="" name="image_url" id="image_url">
                                        @error('image_url')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Categories (Protein, Calories, Carbs, etc.) -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="protein_id">Protein</label>
                                                <select class="form-control @error('protein_id') is-invalid @enderror"
                                                    name="protein_id" id="protein_id">
                                                    <option>Select</option>
                                                    @foreach ($proteins as $protein)
                                                        <option value="{{ $protein->id }}"
                                                            {{ old('protein_id') == $protein->id ? 'selected' : '' }}>
                                                            {{ $protein->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('protein_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="calory_id">Calories</label>
                                                <select class="form-control @error('calory_id') is-invalid @enderror"
                                                    name="calory_id" id="calory_id">
                                                    <option>Select</option>
                                                    @foreach ($calories as $calorie)
                                                        <option value="{{ $calorie->id }}"
                                                            {{ old('calory_id') == $calorie->id ? 'selected' : '' }}>
                                                            {{ $calorie->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('calory_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="carb_id">Carbs</label>
                                                <select class="form-control @error('carb_id') is-invalid @enderror"
                                                    name="carb_id" id="carb_id">
                                                    <option>Select</option>
                                                    @foreach ($carbs as $carb)
                                                        <option value="{{ $carb->id }}"
                                                            {{ old('carb_id') == $carb->id ? 'selected' : '' }}>
                                                            {{ $carb->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('carb_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cuisine_id">Cuisine</label>
                                                <select class="form-control @error('cuisine_id') is-invalid @enderror"
                                                    name="cuisine_id" id="cuisine_id">
                                                    <option>Select</option>
                                                    @foreach ($cuisines as $cuisine)
                                                        <option value="{{ $cuisine->id }}"
                                                            {{ old('cuisine_id') == $cuisine->id ? 'selected' : '' }}>
                                                            {{ $cuisine->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('cuisine_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="health_goal_id">Health Goal</label>
                                                <select class="form-control @error('health_goal_id') is-invalid @enderror"
                                                    name="health_goal_id" id="health_goal_id">
                                                    <option>Select</option>
                                                    @foreach ($health_goals as $goal)
                                                        <option value="{{ $goal->id }}"
                                                            {{ old('health_goal_id') == $goal->id ? 'selected' : '' }}>
                                                            {{ $goal->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('health_goal_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="time_to_clock_id">Time to Clock</label>
                                                <select class="form-control @error('time_to_clock_id') is-invalid @enderror"
                                                    name="time_to_clock_id" id="time_to_clock_id">
                                                    <option>Select</option>
                                                    @foreach ($time_to_cooks as $time_to_cook)
                                                        <option value="{{ $time_to_cook->id }}"
                                                            {{ old('time_to_clock_id') == $time_to_cook->id ? 'selected' : '' }}>
                                                            {{ $time_to_cook->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('time_to_clock_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category -->
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
                                    </div>

                                    <!-- Ingredients Section -->
                                    <hr>
                                    <div id="sections-wrapper" style="background-color:rgb(220, 242, 245); padding:10px">
                                        <h4>Ingredient Sections</h4>

                                        <div class="section-block" data-index="0" style="position: relative;">
                                            <div class="form-group" style="position: relative; padding-right: 233px;">
                                                <label>Section Title</label>
                                                <input type="text" name="sections[0][title]" class="form-control" required>
                                            </div>
                                            <div class="form-group" style="position: relative; padding-right: 233px;">
                                                <label>Order</label>
                                                <input type="number" name="sections[0][order]" class="form-control" value="1" required>
                                            </div>

                                            <div class="ingredients-wrapper">
                                                <h5>Ingredients</h5>
                                                <div class="ingredient-group" data-i="0" style="position: relative; padding-right: 233px;">
                                                    <input type="text" name="sections[0][ingredients][0][name]" class="form-control mb-1" placeholder="Name" required>
                                                    <input type="text" name="sections[0][ingredients][0][amount]" class="form-control mb-1" placeholder="Amount (e.g. 100 grams)" required>
                                                    <div class="form-check my-2">
                                                        <input type="checkbox" class="form-check-input" name="sections[0][ingredients][0][is_highlighted]" value="1">
                                                        <label class="form-check-label">Highlight this ingredient</label>
                                                    </div>
                                                    <button type="button" class="btn btn-danger btn-sm remove-ingredient" style="position: absolute; right: 10px; top: 10px;">Remove Ingredient</button>
                                                    <hr>
                                                </div>
                                            </div>

                                            <button type="button" class="btn btn-sm btn-secondary add-ingredient" style="float: right; margin-top: -22px;">Add Ingredient</button>

                                            <!-- Remove Section button -->
                                            <button type="button" class="btn btn-danger btn-sm remove-section" style="position: absolute; right: 10px; top: -17px;">Remove Section</button>
                                            <hr>
                                        </div>
                                    </div>

                                    <button type="button" id="add-section" class="btn btn-secondary mt-4" style="float: right;">Add Section</button>

                                    <!-- Instructions Section -->
                                    <div id="instructions-wrapper" style="clear: both;">
                                        <h3>Instructions</h3>
                                        <div class="instruction-step" style="position: relative;">
                                            <div class="form-group">
                                                <label for="title">Instruction Title</label>
                                                <input type="text" name="instructions[0][title]" class="form-control" placeholder="Step Title (e.g., 'Step 1: Prepare Ingredients')" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="step_number">Step Number</label>
                                                <input type="number" name="instructions[0][step_number]" class="form-control" placeholder="Step Number" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="instructions[0][description]" class="form-control" placeholder="Instruction Description" required></textarea>
                                            </div>

                                            <!-- Image Upload for Instruction -->
                                            <div class="form-group">
                                                <label for="instruction_image">Step Image (Optional)</label>
                                                <input type="file" name="instructions[0][image_url]" class="form-control" accept="image/*">
                                            </div>

                                            <button type="button" class="btn btn-danger remove-instruction" style="float: right; margin-bottom: 15px;">Remove Instruction</button>
                                        </div>
                                    </div>

                                    <button type="button" id="add-instruction" class="btn btn-secondary mb-2" style="float: right;">Add Instruction</button>

                                    <!-- Submit Button -->
                                    <div class="form-group" style="clear: both;">
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
document.addEventListener('DOMContentLoaded', function () {
    // Add instruction step dynamically
    document.getElementById('add-instruction').addEventListener('click', function() {
        var instructionStep = document.createElement('div');
        instructionStep.classList.add('instruction-step');
        const index = document.querySelectorAll('.instruction-step').length;
        instructionStep.style.position = 'relative';
        instructionStep.innerHTML = `
            <div class="form-group">
                <label for="title">Instruction Title</label>
                <input type="text" name="instructions[${index}][title]" class="form-control" placeholder="Step Title (e.g., 'Step 1: Prepare Ingredients')" required>
            </div>
            <div class="form-group">
                <label for="step_number">Step Number</label>
                <input type="number" name="instructions[${index}][step_number]" class="form-control" placeholder="Step Number" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="instructions[${index}][description]" class="form-control" placeholder="Instruction Description" required></textarea>
            </div>
            <div class="form-group">
                <label for="instruction_image">Step Image (Optional)</label>
                <input type="file" name="instructions[${index}][image_url]" class="form-control" accept="image/*">
            </div>
            <button type="button" class="btn btn-danger remove-instruction" style="float: right; margin-bottom: 15px;">Remove Instruction</button>
        `;
        document.getElementById('instructions-wrapper').appendChild(instructionStep);

        // Add remove functionality for instruction
        instructionStep.querySelector('.remove-instruction').addEventListener('click', function() {
            instructionStep.remove();
        });
    });

    let sectionIndex = 1;

    document.getElementById('add-section').addEventListener('click', function() {
        const wrapper = document.getElementById('sections-wrapper');

        const html = `
        <div class="section-block" data-index="${sectionIndex}" style="position: relative;">
            <div class="form-group" style="position: relative; padding-right: 233px;">
                <label>Section Title</label>
                <input type="text" name="sections[${sectionIndex}][title]" class="form-control" required>
            </div>
            <div class="form-group" style="position: relative; padding-right: 233px;">
                <label>Order</label>
                <input type="number" name="sections[${sectionIndex}][order]" class="form-control" value="${sectionIndex + 1}" required>
            </div>
            <div class="ingredients-wrapper">
                <h5>Ingredients</h5>
                <div class="ingredient-group" data-i="0" style="position: relative; padding-right: 233px;">
                    <input type="text" name="sections[${sectionIndex}][ingredients][0][name]" class="form-control mb-1" placeholder="Name" required>
                    <input type="text" name="sections[${sectionIndex}][ingredients][0][amount]" class="form-control mb-1" placeholder="Amount" required>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="sections[${sectionIndex}][ingredients][0][is_highlighted]" value="1">
                        <label class="form-check-label">Highlight this ingredient</label>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-ingredient" style="position: absolute; right: 10px; top: -17px;">Remove Ingredient</button>
                    <hr>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary add-ingredient" style="float: right; margin-bottom: 15px;">Add Ingredient</button>

            <!-- Remove Section button -->
            <button type="button" class="btn btn-danger btn-sm remove-section" style="position: absolute; right: 10px; top: 10px;">Remove Section</button>
            <hr>
        </div>`;

        wrapper.insertAdjacentHTML('beforeend', html);
        sectionIndex++;
    });

    document.addEventListener('click', function(e) {
        // Add ingredient button
        if (e.target && e.target.classList.contains('add-ingredient')) {
            const sectionBlock = e.target.closest('.section-block');
            const index = sectionBlock.getAttribute('data-index');
            const ingredientsWrapper = sectionBlock.querySelector('.ingredients-wrapper');
            const currentIngredients = ingredientsWrapper.querySelectorAll('.ingredient-group').length;

            const newIngredient = `
            <div class="ingredient-group" data-i="${currentIngredients}" style="position: relative; padding-right: 233px;">
                <input type="text" name="sections[${index}][ingredients][${currentIngredients}][name]" class="form-control mb-1" placeholder="Name" required>
                <input type="text" name="sections[${index}][ingredients][${currentIngredients}][amount]" class="form-control mb-1" placeholder="Amount" required>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="sections[${index}][ingredients][${currentIngredients}][is_highlighted]" value="1">
                    <label class="form-check-label">Highlight this ingredient</label>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-ingredient" style="position: absolute; right: 10px; top: 10px;">Remove Ingredient</button>
                <hr>
            </div>`;

            ingredientsWrapper.insertAdjacentHTML('beforeend', newIngredient);
        }

        // Remove ingredient button
        if (e.target && e.target.classList.contains('remove-ingredient')) {
            const ingredientGroup = e.target.closest('.ingredient-group');
            if (ingredientGroup) {
                ingredientGroup.remove();
            }
        }

        // Remove instruction button
        if (e.target && e.target.classList.contains('remove-instruction')) {
            const instructionStep = e.target.closest('.instruction-step');
            if (instructionStep) {
                instructionStep.remove();
            }
        }

        // Remove section button
        if (e.target && e.target.classList.contains('remove-section')) {
            const sectionBlock = e.target.closest('.section-block');
            if (sectionBlock) {
                sectionBlock.remove();
            }
        }
    });
});
</script>
@endpush
