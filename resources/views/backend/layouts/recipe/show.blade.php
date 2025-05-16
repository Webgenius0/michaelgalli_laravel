@extends('backend.app', ['title' => 'Show Post'])

@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Post</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Post</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Show</li>
                        </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">{{ Str::limit($recipe->title, 50) }}</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- Title --}}
                                <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ $recipe->title }}</h1>
                                <h1 class="text-4xl font-bold text-gray-900 mb-8">{{ "Description - " }}</h1>


                                {{-- Short Description --}}
                                @if ($recipe->short_description)
                                    <p class="text-4xl font-bold text-gray-900 mb-8">{{ $recipe->short_description }}</p>
                                @endif

                                {{-- Long Description --}}
                                @if ($recipe->long_description)
                                    <div class="text-4xl font-bold text-gray-900 mb-8">
                                        {!! nl2br(e($recipe->long_description)) !!}
                                    </div>
                                @endif

                                {{-- INGREDIENTS --}}
                                <div class="mb-12">
                                    <h2 class="text-2xl font-semibold text-green-600 mb-4 flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Ingredients
                                    </h2>

                                    @foreach ($recipe->ingredientSections as $section)
                                        <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $section->title }}</h3>
                                            <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-2">
                                                @foreach ($section->ingredients as $ingredient)
                                                    <li class="flex items-start space-x-2">
                                                        <div class="w-1.5 h-1.5 mt-2 rounded-full bg-green-500"></div>
                                                        <span
                                                            class="{{ $ingredient->is_highlighted ? 'font-semibold text-green-700' : 'text-gray-700' }}">
                                                            {{ $ingredient->amount }} – {{ $ingredient->name }}
                                                            @if ($ingredient->is_highlighted)
                                                                <span
                                                                    class="inline-block ml-1 px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">highlight</span>
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- INSTRUCTIONS --}}
                                <div>
                                    <h2 class="text-2xl font-semibold text-blue-600 mb-4 flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6l4 2m6 4a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Instructions
                                    </h2>

                                    <div class="space-y-6">
                                        @foreach ($recipe->instructions->sortBy('step_number') as $step)
                                            <div
                                                class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                                                <div class="flex items-start justify-between mb-2">
                                                    <div>
                                                        <h4 class="text-lg font-semibold text-gray-800">
                                                            Step {{ $step->step_number }}
                                                            @if ($step->title)
                                                                – {{ $step->title }}
                                                            @endif
                                                        </h4>
                                                    </div>
                                                    @if ($step->image_url)
                                                        <div class="ml-4">
                                                            <img src="{{ asset('storage/' . $step->image_url) }}"
                                                                alt="Step Image"
                                                                class="w-32 h-24 object-cover rounded-md shadow-md border">
                                                        </div>
                                                    @endif
                                                </div>
                                                @if ($step->description)
                                                    <p class="text-gray-700 leading-relaxed">{{ $step->description }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- COL END -->
                </div>

            </div>
        </div>
    </div>
    <!-- CONTAINER CLOSED -->
@endsection
@push('scripts')
    <script>
        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }

        // Delete Button
        function deleteItem(id) {
            NProgress.start();
            let url = "{{ route('admin.post.destroy', ':id') }}";
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    NProgress.done();
                    toastr.success(resp.message);
                    window.location.href = "{{ route('admin.post.index') }}";
                },
                error: function(error) {
                    NProgress.done();
                    toastr.error(error.message);
                }
            });
        }

        //edit
        function goToEdit(id) {
            let url = "{{ route('admin.post.edit', ':id') }}";
            window.location.href = url.replace(':id', id);
        }
    </script>
@endpush
