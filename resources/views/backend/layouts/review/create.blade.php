@extends('backend.app', ['title' => 'Create review'])

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Create review</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">review</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </div>
                </div>

                <div class="row" id="user-profile">
                    <div class="col-lg-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Create review</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body border-0">
                                <form class="form-horizontal" method="POST" action="{{ route('admin.review.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Recipe Title -->
                                    <div class="form-group">
                                        <label for="customer_name">Customer Name</label>
                                        <input type="text" name="customer_name" placeholder="Enter customer name" class="form-control"
                                            value="{{ old('customer_name') }}" required>
                                    </div>

                                     <!-- Recipe Image URL -->
                                    <div class="form-group">
                                        <label for="customer_image" class="form-label">Customer Image:</label>
                                        <input type="file"
                                            class="dropify form-control @error('customer_image') is-invalid @enderror"
                                            data-default-file="" name="customer_image" id="customer_image">
                                        @error('customer_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Short Description -->
                                    <div class="form-group">
                                        <label for="content">Short Review</label>
                                        <textarea name="content" placeholder="Enter review" class="form-control" rows="3">{{ old('content') }}</textarea>
                                    </div>



                                     <div class="form-group">
                                        <label for="rating">Rating</label>
                                        <input type="text" name="rating" placeholder="Enter customer name" class="form-control"
                                            value="{{ old('rating') }}" required>
                                    </div>





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
