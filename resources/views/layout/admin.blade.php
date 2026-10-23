@extends('layout.app')

@section('hide_site_chrome', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}?v={{ filemtime(public_path('css/admin-dashboard.css')) }}">
    <script>
        try {
            if (localStorage.getItem('adminSidebar') === 'collapsed') {
                document.documentElement.classList.add('sb-collapsed');
            }
        } catch (e) {}
    </script>
@endpush

@section('content')
    @php
        $brandName = $settings['brand_name'] ?? 'Furni';

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
                    // Khusus Super Admin: kelola akun administrator (tambah, hapus, ubah status)
                    ['label' => 'Manajemen Admin', 'route' => 'admin.admins.index', 'match' => ['admin.admins.*'],
                        'roles' => ['super_admin']],
                ],
            ],
            [
                'label' => 'Log Aktivitas', 'icon' => 'fa-clock-rotate-left',
                'route' => 'admin.activity-logs.index', 'match' => ['admin.activity-logs.*'],
                'roles' => ['super_admin'],
            ],
            [
                'label' => 'Pengaturan', 'icon' => 'fa-cogs',
                'route' => 'admin.settings.index', 'match' => ['admin.settings.*'],
            ],
        ];

        // Sembunyikan menu yang tidak boleh dilihat role yang sedang login
        $currentRole = auth()->user()->role ?? null;
        $canSee = function ($item) use ($currentRole) {
            return empty($item['roles']) || in_array($currentRole, $item['roles']);
        };

        $adminMenu = collect($adminMenu)
            ->filter($canSee)
            ->map(function ($item) use ($canSee) {
                if (isset($item['children'])) {
                    $item['children'] = array_values(array_filter($item['children'], $canSee));
                }

                return $item;
            })
            ->values()
            ->all();

        // Versi "rata" untuk layar kecil (strip menu horizontal tidak cocok untuk submenu)
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
            <aside class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-head">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">{{ $brandName }}<span>.</span></a>
                    <button type="button" class="sidebar-collapse" id="sidebarCollapse"
                        aria-label="Ciutkan sidebar" aria-expanded="true" title="Ciutkan sidebar">
                        <i class="fa fa-angles-left" aria-hidden="true"></i>
                    </button>
                </div>

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
                                        $first = $item['children'][0];
                                    @endphp
                                    <div class="sidebar-group {{ $groupActive ? 'is-open' : '' }}"
                                        data-group="{{ \Illuminate\Support\Str::slug($item['label']) }}"
                                        data-active="{{ $groupActive ? '1' : '0' }}">
                                        <div class="sidebar-parent">
                                            <a href="{{ route($first['route'], $first['params'] ?? []) }}"
                                                class="sidebar-link {{ $groupActive ? 'is-parent-active' : '' }}">
                                                <i class="fa {{ $item['icon'] }}"></i>
                                                <span class="sidebar-text">{{ $item['label'] }}</span>
                                            </a>
                                            <button type="button" class="sidebar-toggle"
                                                aria-expanded="{{ $groupActive ? 'true' : 'false' }}"
                                                aria-label="Buka atau tutup submenu {{ $item['label'] }}">
                                                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <ul class="sidebar-sub">
                                            <li class="sidebar-sub-title">{{ $item['label'] }}</li>
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
                                    </div>
                                @else
                                    @php $active = $isActive($item); @endphp
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                        class="sidebar-link {{ $active ? 'is-active' : '' }}"
                                        data-tip="{{ $item['label'] }}"
                                        @if($active) aria-current="page" @endif>
                                        <i class="fa {{ $item['icon'] }}"></i>
                                        <span class="sidebar-text">{{ $item['label'] }}</span>
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
                            <a href="{{ route('home') }}" class="sidebar-link" data-tip="Lihat Toko">
                                <i class="fa fa-store"></i>
                                <span class="sidebar-text">Lihat Toko</span>
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="sidebar-link sidebar-link--danger" data-tip="Keluar">
                                    <i class="fa fa-right-from-bracket"></i>
                                    <span class="sidebar-text">Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>

                    @auth
                        <div class="sidebar-user" title="{{ auth()->user()->name }}">
                            <span class="user-avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            <div class="who">
                                <strong>{{ auth()->user()->name }}</strong>
                                <span>{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    @endauth
                </div>
            </aside>

                <script src="{{ asset('js/sidebar.js') }}"></script>

            <!-- KONTEN -->
            <main class="admin-main">
                @yield('admin_content')
            </main>

        </div>
    </div>
@endsection
