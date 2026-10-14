@extends('layout.app')

{{--
    Layout khusus halaman admin: sidebar kiri + konten kanan, tanpa navbar & footer situs.
    Simpan di: resources/views/layout/admin.blade.php
    Halaman admin cukup: @extends('layout.admin') lalu isi @section('admin_content').
--}}

{{-- Memberi tahu layout.app agar navbar & footer situs disembunyikan --}}
@section('hide_site_chrome', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
    @php
        $adminMenu = [
            ['route' => 'admin.dashboard',       'match' => 'admin.dashboard',   'icon' => 'fa-home',          'label' => 'Dashboard'],
            ['route' => 'admin.products.index',  'match' => 'admin.products.*',  'icon' => 'fa-box',           'label' => 'Produk'],
            ['route' => 'admin.orders.index',    'match' => 'admin.orders.*',    'icon' => 'fa-shopping-cart', 'label' => 'Pesanan'],
            ['route' => 'admin.returns.index',   'match' => 'admin.returns.*',   'icon' => 'fa-undo',          'label' => 'Retur'],
            ['route' => 'admin.coupons.index',   'match' => 'admin.coupons.*',   'icon' => 'fa-tags',          'label' => 'Kupon'],
            ['route' => 'admin.users.index',     'match' => 'admin.users.*',     'icon' => 'fa-users',         'label' => 'Pengguna'],
            ['route' => 'admin.settings.index',  'match' => 'admin.settings.*',  'icon' => 'fa-cogs',          'label' => 'Pengaturan'],
        ];
    @endphp

    <div class="admin-dash">
        <div class="admin-shell">

            <!-- SIDEBAR -->
            <aside class="admin-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">{{ $settings['brand_name'] ?? 'Furni' }}<span>.</span></a>

                <nav aria-label="Menu admin">
                    <div class="sidebar-label">Menu</div>
                    <ul class="sidebar-menu">
                        @foreach($adminMenu as $item)
                            @php $active = request()->routeIs($item['match']); @endphp
                            <li>
                                <a href="{{ route($item['route']) }}"
                                    class="sidebar-link {{ $active ? 'is-active' : '' }}"
                                    @if($active) aria-current="page" @endif>
                                    <i class="fa {{ $item['icon'] }}"></i> {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="sidebar-foot">
                    <ul class="sidebar-menu">
                        <li>
                            <a href="{{ route('home') }}" class="sidebar-link">
                                <i class="fa fa-store"></i> Lihat Toko
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="sidebar-link sidebar-link--danger">
                                    <i class="fa fa-right-from-bracket"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>

                    @auth
                        <div class="sidebar-user">
                            <span class="user-avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            <div class="who">
                                <strong>{{ auth()->user()->name }}</strong>
                                <span>{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    @endauth
                </div>
            </aside>

            <!-- KONTEN -->
            <main class="admin-main">
                @yield('admin_content')
            </main>

        </div>
    </div>
@endsection
