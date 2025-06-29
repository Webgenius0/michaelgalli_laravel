@extends('backend.app', ['title' => 'Edit Meal Plan'])

@section('content')
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">


                <div class="page-header">
                    <div>
                        <h1 class="page-title">Edit Meal Plan</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.meal_plan.index') }}">Meal Plan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </div>
                </div>

                <div class="row" id="user-profile">
                    <div class="col-lg-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Edit Meal Plan</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body border-0">
                                <form method="POST" action="{{ route('admin.meal_plan_option.update', $encryptedId) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')

                                    <!-- plan name, people, like that -->

                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="meal_plan_id">Meal Plan</label>
                                                <select class="form-control @error('meal_plan_id') is-invalid @enderror"
                                                    name="meal_plan_id" id="meal_plan_id">
                                                    <option>Select</option>
                                                    @foreach ($meal_plans as $meal_plan)
                                                        <option value="{{ $meal_plan->id }}"
                                                            {{ old('meal_plan_id', $meal_plan_option->meal_plan_id) == $meal_plan->id ? 'selected' : '' }}>
                                                            {{ $meal_plan->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('meal_plan_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="recipes_per_week" class="form-label">Recipes per Week</label>
                                                <input type="number"
                                                    class="form-control @error('recipes_per_week') is-invalid @enderror"
                                                    name="recipes_per_week" placeholder="Enter recipe per week "
                                                    id="" value="{{ old('recipes_per_week', $meal_plan_option->recipes_per_week) }}">
                                                @error('recipes_per_week')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>



                                            <div class="form-group">
                                                <label for="price_per_serving" class="form-label">
                                                    Price per Serving (AED)
                                                </label>
                                                <input type="number"
                                                    class="form-control @error('price_per_serving') is-invalid @enderror"
                                                    name="price_per_serving" placeholder="Enter recipe per week "
                                                    id="" value="{{ old('price_per_serving', $meal_plan_option->price_per_serving) }}">
                                                @error('price_per_serving')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>



                                        <div class="form-group">
                                            <label for="is_recommanded" class="form-label">Recommended</label><br>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_recommanded"
                                                       name="is_recommanded" value="1"
                                                       {{ old('is_recommanded', $meal_plan_option->is_recommanded) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_recommanded">Mark as Recommended</label>
                                            </div>
                                        </div>



                                        </div>

                                        <!-- Submit -->
                                        <div class="form-group" style="clear: both;">
                                            <button class="btn btn-primary" type="submit">Update </button>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection

@push('scripts')
@endpush
