@extends('backend.app', ['title' => 'Orders Management'])

@push('styles')
    <link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
@endpush


@section('content')
    <!--app-content open-->
    <div class="app-content main-content mt-0">
        <div class="side-app">

            <!-- CONTAINER -->
            <div class="main-container container-fluid">


                <!-- PAGE-HEADER -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Orders Management</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Orders Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Index</li>
                        </ol>
                    </div>
                </div>
                <!-- PAGE-HEADER END -->

                <!-- ROW-4 -->
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="card product-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">List</h3>
                                <div class="card-options ms-auto">
                                    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">Add</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="">
                                    <table class="table table-bordered text-nowrap border-bottom" id="datatable">
                                        <thead>
                                            <tr>
                                                <th class="bg-transparent border-bottom-0 wp-15">ID</th>
                                                <th class="bg-transparent border-bottom-0 wp-15">User Information</th>
                                                <th class="bg-transparent border-bottom-0 wp-15">Billing Information</th>

                                                <th class="bg-transparent border-bottom-0 wp-15">Week</th>
                                                <th class="bg-transparent border-bottom-0 wp-15">Total Price</th>
                                                <th class="">Total Recipe</th>

                                                <th class="bg-transparent border-bottom-0 wp-15">Status</th>
                                                <th class="bg-transparent border-bottom-0">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div><!-- COL END -->
                </div>
                <!-- ROW-4 END -->

            </div>
        </div>
    </div>
    <!-- CONTAINER CLOSED -->
@endsection



@push('scripts')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            if (!$.fn.DataTable.isDataTable('#datatable')) {
                let dTable = $('#datatable').DataTable({
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    processing: true,
                    responsive: true,
                    serverSide: true,

                    language: {
                        processing: `<div class="text-center">
                        <img src="{{ asset('default/loader.gif') }}" alt="Loader" style="width: 50px;">
                        </div>`
                    },

                    scroller: {
                        loadingIndicator: false
                    },
                    pagingType: "full_numbers",
                    dom: "<'row justify-content-between table-topbar'<'col-md-4 col-sm-3'l><'col-md-5 col-sm-5 px-0'f>>tipr",
                    ajax: {
                        url: "{{ route('admin.orders.index') }}",
                        type: "GET",
                    },

                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'user',
                            name: 'user',
                            orderable: true,
                            searchable: true
                        },

                        {
                            data: 'billing_info',
                            name: 'billing_info',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'week',
                            name: 'week',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'total_price',
                            name: 'total_price',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'total_recipe',
                            name: 'total_recipe',
                            orderable: false,
                            searchable: false
                        },

                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },




                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'dt-center text-center'
                        },
                    ],
                });
            }
        });

        // Status Change Confirm Alert
        function showStatusChangeAlert(id) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to update the status?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    statusChange(id);
                }
            });
        }



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
            let url = "{{ route('admin.orders.destroy', ':id') }}";
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
                    $('#datatable').DataTable().ajax.reload();
                },
                error: function(error) {
                    NProgress.done();
                    toastr.error(error.message);
                }
            });
        }

        //edit
        function goToEdit(id) {
            let url = "{{ route('admin.orders.edit', ':id') }}";
            window.location.href = url.replace(':id', encodeURIComponent(id));
        }




        // View (Show)
        function goToOpen(id) {
            let url = "{{ route('admin.orders.show', ':id') }}";
            window.location.href = url.replace(':id', encodeURIComponent(id));
        }
    </script>


    <script>
        $(document).on('change', '.status-update', function() {
            let orderId = $(this).data('id');
            let newStatus = $(this).val();

            Swal.fire({
                title: 'Change Status?',
                text: `Update status to "${newStatus}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, update',
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = "{{ route('admin.orders.status', ':id') }}".replace(':id', orderId);


                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            status: newStatus,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            $('#datatable').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            toastr.error('Failed to update status.');
                        }
                    });
                } else {
                    $('#datatable').DataTable().ajax.reload(null, false);
                }
            });
        });
    </script>
@endpush
