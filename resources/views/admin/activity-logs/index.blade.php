@extends('layout.admin')

@section('title', 'Log Aktivitas - ' . ($settings['brand_name'] ?? 'Furni'))

@section('admin_content')
    <div class="page-head">
        <div>
            <h1>Log Aktivitas</h1>
            <p>Riwayat login dan perubahan data yang dilakukan administrator.</p>
        </div>
    </div>

    <!-- LOGIN TERAKHIR TIAP ADMIN -->
    <div class="presence-grid">
        @foreach($admins as $adm)
            <div class="presence-card">
                <span class="user-avatar" aria-hidden="true">{{ strtoupper(mb_substr($adm->name, 0, 1)) }}</span>
                <div class="presence-main">
                    <strong>{{ $adm->name }}</strong>
                    <span class="presence-role">{{ $adm->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
                    <span class="presence-time">
                        @if(isset($lastLogins[$adm->id]))
                            Login terakhir: {{ \Carbon\Carbon::parse($lastLogins[$adm->id])->format('d M Y, H:i') }}
                        @else
                            Belum ada login tercatat
                        @endif
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FILTER -->
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="log-filters">
        <div class="log-filter">
            <label for="f-admin" class="ad-label">Admin</label>
            <select name="admin" id="f-admin" class="ad-input">
                <option value="">Semua admin</option>
                @foreach($admins as $adm)
                    <option value="{{ $adm->id }}" {{ (string) request('admin') === (string) $adm->id ? 'selected' : '' }}>
                        {{ $adm->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="log-filter">
            <label for="f-action" class="ad-label">Jenis aktivitas</label>
            <select name="action" id="f-action" class="ad-input">
                <option value="">Semua aktivitas</option>
                @foreach(\App\Models\ActivityLog::ACTIONS as $code => $meta)
                    <option value="{{ $code }}" {{ request('action') === $code ? 'selected' : '' }}>{{ $meta[0] }}</option>
                @endforeach
            </select>
        </div>

        <div class="log-filter">
            <label for="f-from" class="ad-label">Dari tanggal</label>
            <input type="date" name="from" id="f-from" class="ad-input" value="{{ request('from') }}">
        </div>

        <div class="log-filter">
            <label for="f-to" class="ad-label">Sampai tanggal</label>
            <input type="date" name="to" id="f-to" class="ad-input" value="{{ request('to') }}">
        </div>

        <div class="log-filter">
            <label for="f-q" class="ad-label">Cari aktivitas</label>
            <input type="text" name="q" id="f-q" class="ad-input" value="{{ request('q') }}" placeholder="Mis. Produk, Kupon...">
        </div>

        <div class="log-filter-actions">
            <button type="submit" class="btn-submit">Terapkan</button>
            <a href="{{ route('admin.activity-logs.index') }}" class="btn-cancel">Reset</a>
        </div>
    </form>

    @if($errors->any())
        <div class="ad-alert ad-alert--error" role="alert">
            <i class="fa fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TABEL LOG -->
    <div class="panel">
        <div class="table-wrap">
            <table class="orders-table orders-table--logs">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                        <th>Aktivitas</th>
                        <th>Detail</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php $meta = \App\Models\ActivityLog::ACTIONS[$log->action] ?? [$log->action, 'status-unpaid']; @endphp
                        <tr>
                            <td class="log-time">{{ $log->created_at->format('d M Y') }}<span class="cell-sub">{{ $log->created_at->format('H:i:s') }}</span></td>
                            <td>
                                <span class="cell-main">{{ $log->user_name ?? 'Akun dihapus' }}</span>
                                <span class="cell-sub">{{ $log->user_role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
                            </td>
                            <td><span class="status-badge {{ $meta[1] }}">{{ $meta[0] }}</span></td>
                            <td class="log-desc">{{ $log->description }}</td>
                            <td class="log-detail-cell">
                                @if(!empty($log->properties['changes']))
                                    <details class="log-detail">
                                        <summary>{{ count($log->properties['changes']) }} perubahan</summary>
                                        <ul class="log-changes">
                                            @foreach($log->properties['changes'] as $field => $change)
                                                <li>
                                                    <span class="log-field">{{ \App\Models\ActivityLog::fieldLabel($field) }}</span>
                                                    @if($change['old'] !== null)
                                                        <span class="log-old">{{ $change['old'] }}</span>
                                                    @endif
                                                    @if($change['old'] !== null && $change['new'] !== null)
                                                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                    @endif
                                                    @if($change['new'] !== null)
                                                        <span class="log-new">{{ $change['new'] }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @elseif(!empty($log->properties['email']))
                                    <span class="muted">{{ $log->properties['email'] }}</span>
                                @else
                                    <span class="muted">&mdash;</span>
                                @endif
                            </td>
                            <td class="muted" title="{{ $log->user_agent }}">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fa fa-clock-rotate-left"></i>
                                Belum ada aktivitas yang cocok dengan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->total() > 0)
            <div class="panel-foot">
                {{ $logs->links('admin.partials.pagination') }}
                <p class="panel-foot-info">
                    Menampilkan {{ $logs->firstItem() }}&ndash;{{ $logs->lastItem() }} dari {{ $logs->total() }} aktivitas
                </p>
            </div>
        @endif
    </div>
@endsection
