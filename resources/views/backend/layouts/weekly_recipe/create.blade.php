@extends('backend.app', ['title' => 'Create Weekly Recipe'])

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Create Weekly Recipe</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.weekly_recipe.index') }}">Weekly Recipe</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Assign Recipes to a Week</h3>
                                <a href="{{ route('admin.weekly_recipe.index') }}" class="btn btn-sm btn-secondary">Back</a>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.weekly_recipe.store') }}">
                                    @csrf

                                    <!-- Week Start Date -->
                                    <div class="form-group mb-4"> 
                                        <label for="week_start" class="form-label">Week Start Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="week_start" name="week_start"
                                            class="form-control @error('week_start') is-invalid @enderror" required>
                                        @error('week_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Recipes Multi-Select -->
                                    <div class="mb-4  form-group">
                                        <label for="recipe_ids" class="form-label">Choose Recipes <span
                                                class="text-danger">*</span></label>
                                        <select id="recipe_ids" name="recipe_ids[]"
                                            class="form-control select2 @error('recipe_ids') is-invalid @enderror" multiple
                                            required>
                                            @foreach ($recipes as $recipe)
                                                <option value="{{ $recipe->id }}">{{ $recipe->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('recipe_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Assign Recipes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Weeks Section -->
                <div class="row mt-5">
                    <div class="col-lg-10 offset-lg-1">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4 class="card-title">Previously Assigned Weekly Recipes</h4>
                            </div>
                            <div class="card-body">
                                @forelse ($weeks as $week)
                                    <div class="mb-4">
                                        <h5 class="text-primary mb-2">
                                            <i class="fe fe-calendar"></i>
                                            {{ \Carbon\Carbon::parse($week->week_start)->format('F j, Y') }}
                                        </h5>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($week->recipes as $recipe)
                                                <li class="list-group-item">{{ $recipe->title }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @empty
                                    <p class="text-muted">No weekly recipes assigned yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- CONTAINER CLOSED -->
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select recipes",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
