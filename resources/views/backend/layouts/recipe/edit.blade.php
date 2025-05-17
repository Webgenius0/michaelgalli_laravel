@extends('backend.app', ['title' => 'Edit Recipe'])

@section('content')
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">


                <div class="page-header">
                    <div>
                        <h1 class="page-title">Edit Recipe</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.recipe.index') }}">Recipe</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </div>
                </div>

                <div class="row" id="user-profile">
                    <div class="col-lg-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Edit Recipe</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body border-0">
                                <form method="POST" action="{{ route('admin.recipe.update', $encryptedId) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')

                                    <!-- Recipe Title -->
                                    <div class="form-group">
                                        <label for="title">Recipe Title</label>
                                        <input type="text" name="title" placeholder="Enter title" class="form-control"
                                            value="{{ old('title', $recipe->title) }}" required>
                                    </div>

                                    <!-- Short Description -->
                                    <div class="form-group">
                                        <label for="short_description">Short Description</label>
                                        <textarea name="short_description" placeholder="Enter description" class="form-control" rows="3">{{ old('short_description', $recipe->short_description) }}</textarea>
                                    </div>

                                    <!-- Long Description -->
                                    <div class="form-group">
                                        <label for="long_description">Long Description</label>
                                        <textarea name="long_description" class="form-control" placeholder="Enter description" rows="5">{{ old('long_description', $recipe->long_description) }}</textarea>
                                    </div>

                                    <!-- Recipe Image URL -->
                                    <div class="form-group">
                                        <label for="image_url" class="form-label">Thumbnail:</label>
                                        <input type="file"
                                            class="dropify form-control @error('image_url') is-invalid @enderror"
                                            data-default-file="{{ asset($recipe->image_url) }}" name="image_url"
                                            id="image_url">
                                        @error('image_url')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <!-- Extra preview image -->
                                        <div style="margin-top: 15px;">
                                            <img id="extraImagePreview" src="{{ asset($recipe->image_url) }}"
                                                alt="Current Image"
                                                style="max-width: 200px; max-height: 200px; display: block;">
                                        </div>
                                    </div>


                                    <!-- Categories -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="protein_id">Protein</label>
                                                <select class="form-control @error('protein_id') is-invalid @enderror"
                                                    name="protein_id" id="protein_id">
                                                    <option>Select</option>
                                                    @foreach ($proteins as $protein)
                                                        <option value="{{ $protein->id }}"
                                                            {{ old('protein_id', $recipe->protein_id) == $protein->id ? 'selected' : '' }}>
                                                            {{ $protein->name }}
                                                        </option>
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
                                                            {{ old('calory_id', $recipe->calories_id) == $calorie->id ? 'selected' : '' }}>
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
                                                            {{ old('carb_id', $recipe->carb_id) == $carb->id ? 'selected' : '' }}>
                                                            {{ $carb->name }}
                                                        </option>
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
                                                            {{ old('cuisine_id', $recipe->cuisine_id) == $cuisine->id ? 'selected' : '' }}>
                                                            {{ $cuisine->name }}
                                                        </option>
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
                                                            {{ old('health_goal_id', $recipe->health_goal_id) == $goal->id ? 'selected' : '' }}>
                                                            {{ $goal->name }}
                                                        </option>
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
                                                            {{ old('time_to_clock_id', $recipe->time_to_clock_id) == $time_to_cook->id ? 'selected' : '' }}>
                                                            {{ $time_to_cook->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('time_to_clock_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ingredient Sections -->
                                    <hr>
                                    <div id="sections-wrapper" style="background-color:rgb(220, 242, 245); padding:10px">
                                        <h4>Ingredient Sections</h4>
                                        @php $sectionIndex = 0; @endphp
                                        @foreach (old('sections', $recipe->ingredientSections->sortBy('order')->values()) as $sectionKey => $section)
                                            <div class="section-block" data-index="{{ $sectionIndex }}"
                                                style="position: relative;">
                                                <div class="form-group" style="position: relative; padding-right: 233px;">
                                                    <label>Section Title</label>
                                                    <input type="text" name="sections[{{ $sectionIndex }}][title]"
                                                        class="form-control"
                                                        value="{{ old("sections.$sectionIndex.title", $section->title) }}"
                                                        required>
                                                </div>
                                                <div class="form-group" style="position: relative; padding-right: 233px;">
                                                    <label>Order</label>
                                                    <input type="number" name="sections[{{ $sectionIndex }}][order]"
                                                        class="form-control"
                                                        value="{{ old("sections.$sectionIndex.order", $section->order) }}"
                                                        required>
                                                </div>

                                                <div class="ingredients-wrapper">
                                                    <h5>Ingredients</h5>
                                                    @php
                                                        $ingredients = old(
                                                            "sections.$sectionIndex.ingredients",
                                                            $section->ingredients,
                                                        );
                                                    @endphp
                                                    @foreach ($ingredients as $ingredientIndex => $ingredient)
                                                        <div class="ingredient-group" data-i="{{ $ingredientIndex }}"
                                                            style="position: relative; padding-right: 233px;">
                                                            <input type="text"
                                                                name="sections[{{ $sectionIndex }}][ingredients][{{ $ingredientIndex }}][name]"
                                                                class="form-control mb-1" placeholder="Name"
                                                                value="{{ old("sections.$sectionIndex.ingredients.$ingredientIndex.name", $ingredient->name) }}"
                                                                required>
                                                            <input type="text"
                                                                name="sections[{{ $sectionIndex }}][ingredients][{{ $ingredientIndex }}][amount]"
                                                                class="form-control mb-1" placeholder="Amount"
                                                                value="{{ old("sections.$sectionIndex.ingredients.$ingredientIndex.amount", $ingredient->amount) }}"
                                                                required>
                                                            <div class="form-check my-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="sections[{{ $sectionIndex }}][ingredients][{{ $ingredientIndex }}][is_highlighted]"
                                                                    value="1"
                                                                    {{ old("sections.$sectionIndex.ingredients.$ingredientIndex.is_highlighted", $ingredient->is_highlighted) ? 'checked' : '' }}>
                                                                <label class="form-check-label">Highlight this
                                                                    ingredient</label>
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-ingredient"
                                                                style="position: absolute; right: 10px; top: 10px;">Remove
                                                                Ingredient</button>
                                                            <hr>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button" class="btn btn-sm btn-secondary add-ingredient"
                                                    style="float: right; margin-top: -22px;">Add Ingredient</button>

                                                <button type="button" class="btn btn-danger btn-sm remove-section"
                                                    style="position: absolute; right: 10px; top: -17px;">Remove
                                                    Section</button>
                                                <hr>
                                            </div>
                                            @php $sectionIndex++; @endphp
                                        @endforeach
                                    </div>
                                    <button type="button" id="add-section" class="btn btn-secondary mt-4"
                                        style="float: right;">Add Section</button>

                                    <!-- Instructions -->
                                    <div id="instructions-wrapper" style="clear: both;">
                                        <h3>Instructions</h3>
                                        @php
                                            $instructions = old(
                                                'instructions',
                                                $recipe->instructions->sortBy('step_number')->values(),
                                            );
                                            $instructionIndex = 0;
                                        @endphp
                                        @foreach ($instructions as $index => $instruction)
                                            <div class="instruction-step" style="position: relative;">
                                                <div class="form-group">
                                                    <label for="title">Instruction Title</label>
                                                    <input type="text"
                                                        name="instructions[{{ $instructionIndex }}][title]"
                                                        class="form-control" placeholder="Step Title"
                                                        value="{{ old("instructions.$instructionIndex.title", $instruction->title) }}"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="step_number">Step Number</label>
                                                    <input type="number"
                                                        name="instructions[{{ $instructionIndex }}][step_number]"
                                                        class="form-control" placeholder="Step Number"
                                                        value="{{ old("instructions.$instructionIndex.step_number", $instruction->step_number) }}"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea name="instructions[{{ $instructionIndex }}][description]" class="form-control"
                                                        placeholder="Instruction Description" required>{{ old("instructions.$instructionIndex.description", $instruction->description) }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="instruction_image">Step Image (Optional)</label>
                                                    <input type="file"
                                                        name="instructions[{{ $instructionIndex }}][image_url]"
                                                        class="form-control" accept="image/*">
                                                    @if (!empty($instruction->image_url))
                                                        <img src="{{ asset($instruction->image_url) }}"
                                                            alt="Instruction Image" style="width:100px; margin-top:10px;">
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-danger remove-instruction"
                                                    style="float: right; margin-bottom: 15px;">Remove Instruction</button>
                                            </div>
                                            @php $instructionIndex++; @endphp
                                        @endforeach
                                    </div>
                                    <button type="button" id="add-instruction" class="btn btn-secondary mb-2"
                                        style="float: right;">Add Instruction</button>

                                    <!-- Submit -->
                                    <div class="form-group" style="clear: both;">
                                        <button class="btn btn-primary" type="submit">Update Recipe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get current max section index (from existing DOM)
            let sectionIndex = (() => {
                const sections = document.querySelectorAll('.section-block');
                return sections.length ? Math.max(...Array.from(sections).map(s => Number(s.dataset
                    .index))) + 1 : 0;
            })();

            // Get current max instruction index
            let instructionIndex = (() => {
                const instructions = document.querySelectorAll('.instruction-step');
                return instructions.length ? instructions.length : 0;
            })();

            // Add new Section
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

            <button type="button" class="btn btn-danger btn-sm remove-section" style="position: absolute; right: 10px; top: 10px;">Remove Section</button>
            <hr>
        </div>`;

                wrapper.insertAdjacentHTML('beforeend', html);
                sectionIndex++;
            });

            // Add new Instruction
            document.getElementById('add-instruction').addEventListener('click', function() {
                const wrapper = document.getElementById('instructions-wrapper');

                const html = `
        <div class="instruction-step" style="position: relative;">
            <div class="form-group">
                <label for="title">Instruction Title</label>
                <input type="text" name="instructions[${instructionIndex}][title]" class="form-control" placeholder="Step Title (e.g., 'Step 1: Prepare Ingredients')" required>
            </div>
            <div class="form-group">
                <label for="step_number">Step Number</label>
                <input type="number" name="instructions[${instructionIndex}][step_number]" class="form-control" placeholder="Step Number" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="instructions[${instructionIndex}][description]" class="form-control" placeholder="Instruction Description" required></textarea>
            </div>
            <div class="form-group">
                <label for="instruction_image">Step Image (Optional)</label>
                <input type="file" name="instructions[${instructionIndex}][image_url]" class="form-control" accept="image/*">
            </div>
            <button type="button" class="btn btn-danger remove-instruction" style="float: right; margin-bottom: 15px;">Remove Instruction</button>
        </div>`;

                wrapper.insertAdjacentHTML('beforeend', html);
                instructionIndex++;
            });

            // Delegate clicks inside document for dynamically added buttons
            document.addEventListener('click', function(e) {
                // Remove Ingredient
                if (e.target.classList.contains('remove-ingredient')) {
                    const ingredientGroup = e.target.closest('.ingredient-group');
                    if (ingredientGroup) ingredientGroup.remove();
                }

                // Add Ingredient
                if (e.target.classList.contains('add-ingredient')) {
                    const sectionBlock = e.target.closest('.section-block');
                    const sectionIdx = sectionBlock.getAttribute('data-index');
                    const ingredientsWrapper = sectionBlock.querySelector('.ingredients-wrapper');

                    const currentIngredients = ingredientsWrapper.querySelectorAll('.ingredient-group')
                        .length;

                    const html = `
            <div class="ingredient-group" data-i="${currentIngredients}" style="position: relative; padding-right: 233px;">
                <input type="text" name="sections[${sectionIdx}][ingredients][${currentIngredients}][name]" class="form-control mb-1" placeholder="Name" required>
                <input type="text" name="sections[${sectionIdx}][ingredients][${currentIngredients}][amount]" class="form-control mb-1" placeholder="Amount" required>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="sections[${sectionIdx}][ingredients][${currentIngredients}][is_highlighted]" value="1">
                    <label class="form-check-label">Highlight this ingredient</label>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-ingredient" style="position: absolute; right: 10px; top: 10px;">Remove Ingredient</button>
                <hr>
            </div>`;

                    ingredientsWrapper.insertAdjacentHTML('beforeend', html);
                }

                // Remove Section
                if (e.target.classList.contains('remove-section')) {
                    const sectionBlock = e.target.closest('.section-block');
                    if (sectionBlock) sectionBlock.remove();
                }

                // Remove Instruction
                if (e.target.classList.contains('remove-instruction')) {
                    const instructionStep = e.target.closest('.instruction-step');
                    if (instructionStep) instructionStep.remove();
                }
            });
        });
    </script>

    <script>
        document.getElementById('image_url').addEventListener('change', function(event) {
            const preview = document.getElementById('extraImagePreview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // If no file selected, reset to original image (optional)
                preview.src = "{{ asset($recipe->image_url) }}";
            }
        });
    </script>
@endpush
