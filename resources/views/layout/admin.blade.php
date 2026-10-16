@extends('layout.app')

@section('hide_site_chrome', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
@endpush

@section('content')
    @php
        // Item aktif bila nama route cocok dan (kalau ada) parameter query-nya juga cocok.
        $isActive = function ($item) {
            if (! request()->routeIs(...$item['match'])) {
                return false;
            }

            foreach ($item['query'] ?? [] as $key => $value) {
                if (request($key) !== $value) {
                    return false;
                }
            }

            return true;
        };

        $adminMenu = [
            [
                'label' => 'Dashboard', 'icon' => 'fa-home',
                'route' => 'admin.dashboard', 'match' => ['admin.dashboard'],
            ],
            [
                'label' => 'Produk', 'icon' => 'fa-box',
                'children' => [
                    ['label' => 'Daftar Produk', 'route' => 'admin.products.index',  'match' => ['admin.products.index', 'admin.products.edit']],
                    ['label' => 'Tambah Produk', 'route' => 'admin.products.create', 'match' => ['admin.products.create']],
                ],
            ],
            [
                'label' => 'Pesanan', 'icon' => 'fa-shopping-cart',
                'children' => [
                    ['label' => 'Semua Pesanan',  'route' => 'admin.orders.index',  'match' => ['admin.orders.*']],
                    ['label' => 'Retur & Refund', 'route' => 'admin.returns.index', 'match' => ['admin.returns.*']],
                ],
            ],
            [
                'label' => 'Kupon', 'icon' => 'fa-tags',
                'children' => [
                    ['label' => 'Daftar Kupon', 'route' => 'admin.coupons.index',  'match' => ['admin.coupons.index']],
                    ['label' => 'Buat Kupon',   'route' => 'admin.coupons.create', 'match' => ['admin.coupons.create']],
                ],
            ],
            [
                'label' => 'Pengguna', 'icon' => 'fa-users',
                'children' => [
                    ['label' => 'Semua Pengguna', 'route' => 'admin.users.index', 'match' => ['admin.users.*'],
                        'query' => ['status' => null]],
                    ['label' => 'Aktif', 'flat_label' => 'Pengguna Aktif', 'route' => 'admin.users.index', 'match' => ['admin.users.*'],
                        'params' => ['status' => 'active'], 'query' => ['status' => 'active']],
                    ['label' => 'Ditangguhkan', 'flat_label' => 'Pengguna Ditangguhkan', 'route' => 'admin.users.index', 'match' => ['admin.users.*'],
                        'params' => ['status' => 'suspended'], 'query' => ['status' => 'suspended']],
                    ['label' => 'Diblokir', 'flat_label' => 'Pengguna Diblokir', 'route' => 'admin.users.index', 'match' => ['admin.users.*'],
                        'params' => ['status' => 'banned'], 'query' => ['status' => 'banned']],
                ],
            ],
            [
                'label' => 'Pengaturan', 'icon' => 'fa-cogs',
                'route' => 'admin.settings.index', 'match' => ['admin.settings.*'],
            ],
        ];

        $flatMenu = collect($adminMenu)->flatMap(function ($item) {
            if (isset($item['children'])) {
                return collect($item['children'])->map(function ($child) use ($item) {
                    $child['icon'] = $item['icon'];
                    return $child;
                })->all();
            }

            return [$item];
        })->all();
    @endphp

    <div class="admin-dash">
        <div class="admin-shell">

            <!-- SIDEBAR -->
            <aside class="admin-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">{{ $settings['brand_name'] ?? 'Furni' }}<span>.</span></a>

                <nav aria-label="Menu admin">
                    <div class="sidebar-label">Menu</div>

                    {{-- Desktop: menu bertingkat --}}
                    <ul class="sidebar-menu sidebar-menu--tree">
                        @foreach($adminMenu as $item)
                            <li>
                                @if(isset($item['children']))
                                    @php
                                        $groupActive = collect($item['children'])->contains(function ($child) use ($isActive) {
                                            return $isActive($child);
                                        });
                                    @endphp
                                    <details class="sidebar-group" @if($groupActive) open @endif>
                                        <summary class="sidebar-link {{ $groupActive ? 'is-parent-active' : '' }}">
                                            <i class="fa {{ $item['icon'] }}"></i> {{ $item['label'] }}
                                            <i class="fa fa-chevron-down sidebar-caret" aria-hidden="true"></i>
                                        </summary>
                                        <ul class="sidebar-sub">
                                            @foreach($item['children'] as $child)
                                                @php $childActive = $isActive($child); @endphp
                                                <li>
                                                    <a href="{{ route($child['route'], $child['params'] ?? []) }}"
                                                        class="sidebar-sublink {{ $childActive ? 'is-active' : '' }}"
                                                        @if($childActive) aria-current="page" @endif>
                                                        {{ $child['label'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @else
                                    @php $active = $isActive($item); @endphp
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                        class="sidebar-link {{ $active ? 'is-active' : '' }}"
                                        @if($active) aria-current="page" @endif>
                                        <i class="fa {{ $item['icon'] }}"></i> {{ $item['label'] }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    {{-- Layar kecil: semua menu dijajarkan --}}
                    <ul class="sidebar-menu sidebar-menu--flat">
                        @foreach($flatMenu as $item)
                            @php $active = $isActive($item); @endphp
                            <li>
                                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                    class="sidebar-link {{ $active ? 'is-active' : '' }}"
                                    @if($active) aria-current="page" @endif>
                                    <i class="fa {{ $item['icon'] }}"></i> {{ $item['flat_label'] ?? $item['label'] }}
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
