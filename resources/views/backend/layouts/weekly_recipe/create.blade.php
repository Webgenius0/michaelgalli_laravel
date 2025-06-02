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
                                        <input type="text" id="week_start" name="week_start"
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
                @if ($weeks->count())
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Week Start Date</th>
                                    <th>Assigned Recipes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($weeks as $week)
                                    <tr>
                                        <td>{{ $week->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($week->week_start)->format('F j, Y') }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach ($week->recipes as $recipe)
                                                    <div class="d-flex align-items-center border p-2 "
                                                        style="min-width: 220px;">
                                                        <img src="{{ asset($recipe->image_url) }}"
                                                            alt="{{ $recipe->title }}" class="me-2"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                        <span class="me-2">{{ $recipe->title }}</span>
                                                        <form method="POST"
                                                            action="{{ route('admin.weekly_recipe.recipe.delete', $recipe->id) }}"
                                                            onsubmit="return confirm('Are you sure you want to remove this recipe?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger ms-2">
                                                                <i class="fe fe-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- Add recipe form for this week -->

                                            <form method="POST"
                                                action="{{ route('admin.weekly_recipe.recipe.recipe_add', $week->id) }}"
                                                class="d-flex align-items-center gap-2 mt-2">
                                                @csrf
                                                <select name="recipe_id" class="form-select form-select-sm select2"
                                                    style="width: 250px;">
                                                    <option value="">-- Select Recipe --</option>
                                                    @foreach ($recipes as $recipe)
                                                        @if (!in_array($recipe->id, $week->recipes->pluck('id')->toArray()))
                                                            <option value="{{ $recipe->id }}">
                                                                {{ $recipe->title }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-success">Add</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No weekly recipes assigned yet.</p>
                @endif

            </div>
            <!-- CONTAINER CLOSED -->
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#week_start").datepicker({
                dateFormat: "yy-mm-dd" 
            });
        });
    </script>

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
