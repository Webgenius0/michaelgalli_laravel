@extends('backend.app', ['title' => 'Edit review'])

@section('content')
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">


                <div class="page-header">
                    <div>
                        <h1 class="page-title">Edit review</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.review.index') }}">Review</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </div>
                </div>

                <div class="row" id="user-profile">
                    <div class="col-lg-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Edit review</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body border-0">
                                <form method="POST" action="{{ route('admin.review.update', $encryptedId) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')

                                    <!-- review Title -->
                                    <div class="form-group">
                                        <label for="customer_name">Customer name</label>
                                        <input type="text" name="customer_name" placeholder="Enter customer_name" class="form-control"
                                            value="{{ old('customer_name', $review->customer_name) }}" required>
                                    </div>



                                    <!-- review Image URL -->
                                    <div class="form-group">
                                        <label for="customer_image" class="form-label">Customer Image:</label>
                                        <input type="file"
                                            class="dropify form-control @error('customer_image') is-invalid @enderror"
                                            data-default-file="{{ asset($review->customer_image) }}" name="customer_image"
                                            id="customer_image">
                                        @error('customer_image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror

                                        <!-- Extra preview image -->
                                        {{-- <div style="margin-top: 15px;">
                                            <img id="extraImagePreview" src="{{ asset($review->customer_image) }}"
                                                alt="Current Image"
                                                style="max-width: 200px; max-height: 200px; display: block;">
                                        </div> --}}
                                    </div>

                                         <!-- Short Description -->
                                    <div class="form-group">
                                        <label for="content">Short Review</label>
                                        <textarea name="content" placeholder="Enter review" class="form-control" rows="3">{{ old('content', $review->content) }}</textarea>
                                    </div>



                                     <div class="form-group">
                                        <label for="rating">Rating</label>
                                        <input type="text" name="rating" placeholder="Enter customer name" class="form-control"
                                            value="{{ old('rating', $review->rating) }}" required>
                                    </div>




                                    <!-- Submit -->
                                    <div class="form-group" style="clear: both;">
                                        <button class="btn btn-primary" type="submit">Update review</button>
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
                preview.src = "{{ asset($review->image_url) }}";
            }
        });
    </script>
@endpush
