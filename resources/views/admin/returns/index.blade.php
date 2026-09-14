@extends('layout.app')

@section('title', 'Kelola Retur & Refund - Admin Furni')

@section('content')
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1>Kelola Retur & Refund</h1>
                        <p class="text-white-50">Daftar pengajuan retur barang dari pelanggan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="untree_co-section before-footer-section">
        <div class="container">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="site-blocks-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Retur</th>
                                    <th>ID Order</th>
                                    <th>Pelanggan</th>
                                    <th>Alasan Retur</th>
                                    <th>Bukti Foto</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $item)
                                <tr>
                                    <td><strong>#{{ $item->id }}</strong></td>
                                    <td>#{{ $item->order_id }}</td>
                                    <td>{{ $item->user->name ?? 'User Dihapus' }}</td>
                                    <td style="max-width: 250px;">{{ $item->reason }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $item->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                            <i class="fa fa-image me-1"></i> Lihat Foto
                                        </a>
                                    </td>
                                    <td>
                                        @if($item->status == 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                                        @elseif($item->status == 'approved')
                                            <span class="badge bg-success px-3 py-2">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'pending')
                                            <div class="d-flex gap-1">
                                                <form action="{{ route('admin.returns.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui pengajuan retur ini?')">Terima</button>
                                                </form>
                                                <form action="{{ route('admin.returns.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pengajuan retur ini?')">Tolak</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small italic">Selesai diproses</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada pengajuan retur dari pelanggan.</td>
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