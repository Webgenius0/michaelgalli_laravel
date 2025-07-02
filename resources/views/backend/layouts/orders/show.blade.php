@extends('backend.app', ['title' => 'Order Details'])

@section('content')
    <style>
        /* Clean, Professional Styling */
        .app-content {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .page-header {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            list-style: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-item {
            color: #718096;
            font-size: 0.9rem;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: "/";
            margin-left: 8px;
            color: #cbd5e0;
        }

        .breadcrumb-item.active {
            color: #4a5568;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: #4299e1;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: #3182ce;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .card-header {
            background: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
        }

        .btn-primary {
            background-color: #4299e1;
            border-color: #4299e1;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #3182ce;
            border-color: #3182ce;
            color: white;
            text-decoration: none;
        }

        .card-body {
            padding: 30px;
        }

        .row {
            margin: 0 -15px;
        }

        .col-lg-6 {
            padding: 0 15px;
        }

        .info-section {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-section h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .info-section p {
            margin-bottom: 12px;
            line-height: 1.5;
            color: #4a5568;
        }

        .info-section strong {
            color: #2d3748;
            font-weight: 600;
            display: inline-block;
            min-width: 120px;
        }

        .info-section a {
            color: #4299e1;
            text-decoration: none;
        }

        .info-section a:hover {
            color: #3182ce;
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #4299e1;
            color: white;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .text-muted {
            color: #a0aec0 !important;
            font-style: italic;
            text-align: center;
            padding: 20px;
            background: #f7fafc;
            border: 1px dashed #cbd5e0;
            border-radius: 6px;
        }

        .recipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .recipe-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            background: white;
            transition: box-shadow 0.2s;
        }

        .recipe-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .recipe-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .recipe-card .card-body {
            padding: 15px;
        }

        .recipe-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .recipe-card .card-text {
            color: #718096;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        hr {
            border: none;
            height: 1px;
            background-color: #e2e8f0;
            margin: 20px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card-body {
                padding: 20px;
            }

            .info-section {
                margin-bottom: 15px;
            }

            .recipe-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Order Details</h1>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Order</a></li>
                            <li class="breadcrumb-item active">Show</li>
                        </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card post-sales-main">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">Order #{{ $order->id }}</h3>
                                <div class="card-options">
                                    <a href="javascript:window.history.back()" class="btn btn-sm btn-primary">Back</a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        {{-- USER INFO --}}
                                        <div class="info-section">
                                            <h4 class="mb-3">User Information</h4>
                                            <p>
                                                <strong>Name:</strong> {{ $order->user->first_name }}
                                                {{ $order->user->last_name }}<br>
                                                <strong>Email:</strong> <a
                                                    href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a><br>
                                                <strong>Phone:</strong> <a
                                                    href="tel:{{ $order->user->phone_number }}">{{ $order->user->phone_number }}</a>
                                            </p>
                                        </div>

                                        {{-- BILLING INFO --}}
                                        <div class="info-section">
                                            <h4 class="my-3">Billing Information</h4>
                                            @if ($order->user->billing_information)
                                                <p>
                                                    <strong>Name:</strong>
                                                    {{ $order->user->billing_information->full_name }}<br>
                                                    <strong>Email:</strong>
                                                    {{ $order->user->billing_information->email }}<br>
                                                    <strong>Phone:</strong>
                                                    {{ $order->user->billing_information->phone }}<br>
                                                    <strong>Address:</strong>
                                                    {{ $order->user->billing_information->address ?? 'N/A' }}<br>
                                                    <strong>City:</strong> {{ $order->user->billing_information->city }}
                                                </p>
                                            @else
                                                <p class="text-muted">No billing information found.</p>
                                            @endif
                                        </div>

                                        {{-- DELIVERY ADDRESS --}}
                                        <div class="info-section">
                                            <h4>Delivery Address</h4>
                                            @if ($order->user->deliveryAddresses && $order->user->deliveryAddresses->count())
                                                @php $delivery = $order->user->deliveryAddresses->first(); @endphp
                                                <p>
                                                    <strong>Address:</strong> {{ $delivery->address ?? 'N/A' }}<br>
                                                    <strong>City:</strong> {{ $delivery->city ?? 'N/A' }}<br>
                                                    <strong>Postal Code:</strong> {{ $delivery->postal_code ?? 'N/A' }}
                                                </p>
                                            @else
                                                <p class="text-muted">No delivery address found.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        {{-- ORDER INFO --}}
                                        <div class="info-section">
                                            <h4>Order Info</h4>
                                            <p>
                                                <strong>Week:</strong> {{ $order->week_start->format('F d, Y') }}<br>
                                                <strong>Status:</strong> <span
                                                    class="badge bg-info p-2">{{ ucfirst($order->status) }}</span><br>
                                                <strong>Total Price:</strong>
                                                {{ $order->user->plan_cart?->total_price ?? 0 }}
                                                AED<br>
                                                <strong>Total orders:</strong> {{ $order->recipes->count() }}
                                            </p>
                                        </div>

                                        {{-- RECIPE LIST --}}
                                        <div class="info-section">
                                            <h4>Recipes</h4>
                                            @if ($order->recipes->count())
                                                <div class="recipe-grid">
                                                    @foreach ($order->recipes as $rec)
                                                        <div class="recipe-card">
                                                            <img src="{{ asset($rec->recipe->image_url) }}"
                                                                class="card-img-top" alt="{{ $rec->title }}">
                                                            <div class="card-body">
                                                                <h5 class="card-title">{{ $rec->recipe->title }}</h5>
                                                                <p class="card-text">
                                                                    {{ Str::limit($rec->recipe->short_description, 100) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">No recipes found for this order.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <h4 class="mt-4 mb-2">Ingredient Swaps</h4>

                                    @if ($order->order_ingredients->count())
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Recipe</th>
                                                        <th>Family Member</th>
                                                        <th>Original Ingredient</th>
                                                        <th>Swapped Ingredient</th>
                                                        <th>Reason</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($order->order_ingredients as $item)
                                                        <tr>
                                                            <td>{{ $item->recipe->title ?? 'N/A' }}</td>
                                                            <td>{{ $item->userFamilyMember->first_name . " " . $item->userFamilyMember->last_name ?? 'N/A' }}</td>
                                                            <td>{{ $item->original_ingredient }}</td>
                                                            <td>
                                                                @if ($item->swapped_ingredient)
                                                                    <span
                                                                        class="text-success">{{ $item->swapped_ingredient }}</span>
                                                                @else
                                                                    <span class="text-muted">No Swap</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->reason ?? '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No ingredient swaps found for this order.</p>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div><!-- COL END -->
                </div>
            </div>
        </div>
    </div>
@endsection
