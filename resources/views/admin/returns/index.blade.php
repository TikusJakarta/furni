@extends('layout.admin')

@section('title', 'Kelola Retur & Refund - Admin Furni')

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Kelola Retur & Refund</h1>
            <p>Daftar pengajuan retur barang dari pelanggan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="ad-alert ad-alert--success alert alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="panel">
        <div class="table-wrap">
            <table class="orders-table orders-table--returns">
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
                            <td class="order-id">#{{ $item->id }}</td>
                            <td class="num">#{{ $item->order_id }}</td>
                            <td class="cell-main">{{ $item->user->name ?? 'User Dihapus' }}</td>
                            <td class="reason-cell">{{ $item->reason }}</td>
                            <td>
                                <a href="{{ asset('storage/' . $item->proof_image) }}" target="_blank" rel="noopener"
                                    class="btn-proof">
                                    <i class="fa fa-image"></i> Lihat Foto
                                </a>
                            </td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="status-badge status-verify">Pending</span>
                                @elseif($item->status == 'approved')
                                    <span class="status-badge status-paid">Disetujui</span>
                                @else
                                    <span class="status-badge status-other">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'pending')
                                    <div class="row-actions">
                                        <form action="{{ route('admin.returns.update', $item->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn-row btn-row--approve"
                                                onclick="return confirm('Setujui pengajuan retur ini?')">
                                                <i class="fa fa-check"></i> Terima
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.returns.update', $item->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn-row btn-row--delete"
                                                onclick="return confirm('Tolak pengajuan retur ini?')">
                                                <i class="fa fa-times"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="done-note"><i class="fa fa-check-circle"></i> Selesai diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fa fa-undo"></i>
                                Belum ada pengajuan retur dari pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
