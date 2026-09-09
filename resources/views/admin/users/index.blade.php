@extends('layout.app')

@section('title', 'Kelola Pengguna - Admin Furni')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Kelola Pengguna / Customer</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section before-footer-section">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary text-white">&larr; Kembali ke Dashboard</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="site-blocks-table">
                    <table class="table">
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
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <span class="badge 
                                        @if($user->status == 'active') bg-success 
                                        @elseif($user->status == 'suspended') bg-warning text-dark 
                                        @else bg-danger @endif">
                                        {{ strtoupper($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST">
                                        @csrf
                                        <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active (Normal)</option>
                                            <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspend (Tangguhkan)</option>
                                            <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Banned (Blokir)</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pelanggan terdaftar.</td>
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