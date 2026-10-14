@extends('layout.app')

@section('title', 'Kelola Pengguna - Admin Furni')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">

    <div class="admin-dash">

        <!-- HERO -->
        <div class="hero admin-hero">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <h1>Kelola Pengguna / Customer</h1>
                        <p>Pantau akun pelanggan dan atur status aksesnya.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-body">
            <div class="container">

                <div class="page-toolbar">
                    <a href="{{ route('admin.dashboard') }}" class="btn-back">&larr; Kembali ke Dashboard</a>
                </div>

                @if(session('success'))
                    <div class="ad-alert ad-alert--success" role="status">
                        <i class="fa fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="panel">
                    <div class="table-wrap">
                        <table class="orders-table orders-table--users">
                            <thead>
                                <tr>
                                    <th>Nama Pelanggan</th>
                                    <th>Email</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Status Akun</th>
                                    <th>Ubah Status (Aksi)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="user-cell">
                                                <span class="user-avatar" aria-hidden="true">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                                <span class="cell-main">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td class="muted">{{ $user->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            <span class="status-badge
                                                @if($user->status == 'active') status-paid
                                                @elseif($user->status == 'suspended') status-verify
                                                @else status-other @endif">
                                                {{ strtoupper($user->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                                @csrf
                                                <select name="status" class="status-select"
                                                    aria-label="Ubah status akun {{ $user->name }}"
                                                    onchange="this.form.submit()">
                                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active (Normal)</option>
                                                    <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspend (Tangguhkan)</option>
                                                    <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Banned (Blokir)</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-state">
                                            <i class="fa fa-users"></i>
                                            Belum ada pelanggan terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection