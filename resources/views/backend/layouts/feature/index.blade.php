@extends('backend.app', ['title' => 'Create subscription features'])

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Create Subscription features</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Subscription features</a></li>
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
                                <form class="form-horizontal" method="POST" action="{{ route('admin.feature.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Recipe Title -->

                                    <div class="row">


                                        <div class="form-group">
                                            <label for="description" class="form-label">Main Feature :</label>
                                            <textarea cols="5" class="description form-control @error('description') is-invalid @enderror" name="description"
                                                placeholder="Description" id="description">{{ $feature->description ?? (old('description') ?? '') }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label for="include_feature" class="form-label">Include Feature:</label>
                                            <textarea cols="5" class="include_feature form-control @error('include_feature') is-invalid @enderror" name="include_feature"
                                                placeholder="Include feature" id="include_feature">{{ $feature->include_description ?? (old('include_feature') ?? '') }}</textarea>
                                            @error('include_feature')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <input hidden type="text" name="id" value="{{ $feature->id ?? '' }}">


                                    </div>




                                    <!-- Submit Button -->
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
    <!-- CONTAINER CLOSED -->
@endsection

@push('scripts')

<script>
    $('.include_feature').summernote({
        placeholder: 'text',
        tabsize: 2,
        height: 100
    });


</script>


@endpush
