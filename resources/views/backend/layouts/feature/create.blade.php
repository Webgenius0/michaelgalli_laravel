@extends('backend.app', ['title' => 'Create Meal Plan'])

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Create Meal Plan</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Meal Plan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </div>
                </div>

                <div class="row" id="user-profile">
                    <div class="col-lg-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Create Meal Plan</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body border-0">
                                <form class="form-horizontal" method="POST" action="{{ route('admin.meal_plan.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Recipe Title -->

                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="name" class="form-label">Name:</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    placeholder="Enter Name" id="" value="{{ old('name') }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="recipes_per_week" class="form-label">Meal Per Week</label>
                                                <input type="number"
                                                    class="form-control @error('recipes_per_week') is-invalid @enderror"
                                                    name="recipes_per_week" placeholder="Enter recipe per week"
                                                    id="" value="{{ old('recipes_per_week') }}">
                                                @error('recipes_per_week')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="stripe_price_id" class="form-label">Stripe Price ID</label>
                                                <input type="text"
                                                    class="form-control @error('stripe_price_id') is-invalid @enderror"
                                                    name="stripe_price_id" placeholder="Enter recipe per week"
                                                    id="" value="{{ old('stripe_price_id') }}">
                                                @error('stripe_price_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>



                                        </div>

                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="people" class="form-label">People:</label>
                                                <input type="number"
                                                    class="form-control @error('people') is-invalid @enderror"
                                                    name="people" placeholder="Enter number of people" id=""
                                                    value="{{ old('people') }}">
                                                @error('people')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            <div class="form-group">
                                                <label for="price_per_serving" class="form-label">Price per Serving</label>
                                                <input type="number"
                                                    class="form-control @error('price_per_serving') is-invalid @enderror"
                                                    name="price_per_serving" placeholder="Enter recipe per week"
                                                    id="" value="{{ old('price_per_serving') }}">
                                                @error('price_per_serving')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>


                                        </div>
                                    </div>




                                    <!-- Submit Button -->
                                    <div class="form-group" style="clear: both;">
                                        <button class="btn btn-primary" type="submit">Submit </button>
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
   
@endpush
