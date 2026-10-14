@extends('layout.app')

@section('title', 'Pesanan Saya - Furni')

@section('content')
    {{-- Panggil file CSS terpisah --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard-orders.css') }}">

    <!-- Start Hero Section -->
    <div class="hero py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="intro-excerpt">
                        <h1 class="h3 fw-bold text-white mb-1">Pesanan Saya</h1>
                        <p class="text-white-50 mb-0">Kelola dan pantau status riwayat belanja furniture kamu di sini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section pb-5">
        <div class="container">

            {{-- Alert Notifikasi Sukses / Error --}}
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Modern Nav Pills -->
            <ul class="nav nav-pills custom-nav-pills gap-2 mb-4" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-2 rounded-pill fw-semibold" id="all-tab" data-bs-toggle="pill"
                        data-bs-target="#all" type="button" role="tab">Semua Pesanan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 rounded-pill fw-semibold text-secondary" id="pending-tab" data-bs-toggle="pill"
                        data-bs-target="#pending" type="button" role="tab">
                        Belum Dibayar 
                        @if($pendingOrders->count() > 0)
                            <span class="badge bg-warning text-dark ms-1 rounded-pill px-2">{{ $pendingOrders->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 rounded-pill fw-semibold text-secondary" id="processing-tab" data-bs-toggle="pill"
                        data-bs-target="#processing" type="button" role="tab">
                        Diproses 
                        @if($processingOrders->count() > 0)
                            <span class="badge bg-info text-dark ms-1 rounded-pill px-2">{{ $processingOrders->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 rounded-pill fw-semibold text-secondary" id="completed-tab" data-bs-toggle="pill"
                        data-bs-target="#completed" type="button" role="tab">
                        Selesai 
                        @if($completedOrders->count() > 0)
                            <span class="badge bg-success ms-1 rounded-pill px-2">{{ $completedOrders->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="orderTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $orders])
                </div>
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $pendingOrders])
                </div>
                <div class="tab-pane fade" id="processing" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $processingOrders])
                </div>
                <div class="tab-pane fade" id="completed" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $completedOrders])
                </div>
            </div>

        </div>
    </div>
@endsection