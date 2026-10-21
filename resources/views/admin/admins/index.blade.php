@extends('layout.admin')

@section('title', 'Manajemen Admin - ' . ($settings['brand_name'] ?? 'Furni'))

@section('admin_content')
    <div class="page-head">
        <div>
            <p class="welcome-line">Manajemen Akun Administrator</p>
            <p>Kelola siapa saja yang memiliki hak akses ke panel kontrol toko.</p>
        </div>
        <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fa fa-plus"></i> Tambah Admin Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Administrator & Super Admin</h2>
        </div>
        <div class="table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role / Hak Akses</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $adm)
                        <tr>
                            <td class="fw-bold">{{ $adm->name }}</td>
                            <td>{{ $adm->email }}</td>
                            <td>
                                @if($adm->role === 'super_admin')
                                    <span class="status-badge status-completed">Super Admin</span>
                                @else
                                    <span class="status-badge status-process">Admin</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $adm->status === 'active' ? 'status-paid' : 'status-other' }}">
                                    {{ ucfirst($adm->status) }}
                                </span>
                            </td>
                            <td>
                                @if($adm->id !== auth()->id())
                                    <form action="{{ route('admin.admins.destroy', $adm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akses admin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fa fa-trash"></i> Hapus</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Akun Anda</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Tambah Administrator Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.admins.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Kata Sandi</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Level Hak Akses (Role)</label>
                            <select name="role" class="form-control" required>
                                <option value="admin">Admin (Staf / Operasional)</option>
                                <option value="super_admin">Super Admin (Kontrol Penuh)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-dark btn-sm" style="background-color: #2f463d;">Simpan Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection