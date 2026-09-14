@extends('layout.app')

@section('title', 'Pesanan Saya - Furni')

@section('content')
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>Pesanan Saya</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section">
        <div class="container">

            {{-- Alert Notifikasi Sukses / Error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-dark fw-bold" id="all-tab" data-bs-toggle="tab"
                        data-bs-target="#all" type="button" role="tab">Semua Pesanan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark fw-bold" id="pending-tab" data-bs-toggle="tab"
                        data-bs-target="#pending" type="button" role="tab">
                        Belum Dibayar <span class="badge bg-warning text-dark ms-1">{{ $pendingOrders->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark fw-bold" id="processing-tab" data-bs-toggle="tab"
                        data-bs-target="#processing" type="button" role="tab">
                        Diproses <span class="badge bg-info text-dark ms-1">{{ $processingOrders->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark fw-bold" id="completed-tab" data-bs-toggle="tab"
                        data-bs-target="#completed" type="button" role="tab">
                        Selesai <span class="badge bg-success ms-1">{{ $completedOrders->count() }}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="orderTabsContent">
                <!-- Tab Semua Pesanan -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $orders])
                </div>
                <!-- Tab Belum Dibayar -->
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $pendingOrders])
                </div>
                <!-- Tab Diproses -->
                <div class="tab-pane fade" id="processing" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $processingOrders])
                </div>
                <!-- Tab Selesai -->
                <div class="tab-pane fade" id="completed" role="tabpanel">
                    @include('user.partials-order-list-content', ['orders' => $completedOrders])
                </div>
            </div>

        </div>
    </div>
@endsection