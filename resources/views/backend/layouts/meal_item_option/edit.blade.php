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
                                <form method="POST" action="{{ route('admin.meal_plan.update', $encryptedId) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')

                                    <!-- plan name, people, like that -->

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div
                                                class="form-group

                                                @error('name') is-invalid @enderror">
                                                <label for="name" class="form-label">Name:</label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Enter Name" id=""
                                                    value="{{ old('name', $meal_plan->name) }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div
                                                class="form-group
                                                @error('people_count') is-invalid @enderror">
                                                <label for="people_count" class="form-label">Number of People</label>
                                                <input type="number" class="form-control" name="people_count"
                                                    placeholder="Enter number of people" id=""
                                                    value="{{ old('people_count', $meal_plan->people_count) }}">
                                                @error('people_count')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
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
