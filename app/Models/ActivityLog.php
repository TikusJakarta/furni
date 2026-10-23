<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Str;

/**
 * Simpan di: app/Models/ActivityLog.php
 */
class ActivityLog extends Model
{
    use Prunable;

    // Log hanya punya waktu dibuat, tidak pernah diubah
    public const UPDATED_AT = null;

    /** Role yang aktivitasnya dicatat */
    public const TRACKED_ROLES = ['admin', 'super_admin'];

    /** kode aksi => [label, kelas badge] */
    public const ACTIONS = [
        'login'        => ['Login',       'status-paid'],
        'logout'       => ['Logout',      'status-unpaid'],
        'login_failed' => ['Login Gagal', 'status-other'],
        'created'      => ['Tambah',      'status-completed'],
        'updated'      => ['Ubah',        'status-process'],
        'deleted'      => ['Hapus',       'status-other'],
    ];

    /** Nama kolom database => nama yang mudah dibaca di halaman log */
    public const FIELD_LABELS = [
        'name'        => 'Nama',
        'email'       => 'Email',
        'role'        => 'Role',
        'status'      => 'Status',
        'price'       => 'Harga',
        'stock'       => 'Stok',
        'weight'      => 'Berat',
        'type'        => 'Tipe',
        'image'       => 'Gambar',
        'description' => 'Deskripsi',
        'code'        => 'Kode',
        'value'       => 'Nilai',
        'min_spend'   => 'Min. belanja',
        'expires_at'  => 'Berakhir',
        'total_price' => 'Total harga',
        'reason'      => 'Alasan',
    ];

    protected $fillable = [
        'user_id', 'user_name', 'user_role', 'action', 'description',
        'subject_type', 'subject_id', 'properties', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Log yang lebih tua dari 180 hari dihapus oleh `php artisan model:prune` */
    public function prunable()
    {
        return static::where('created_at', '<', now()->subDays(180));
    }

    public static function fieldLabel(string $field): string
    {
        return self::FIELD_LABELS[$field] ?? $field;
    }


    public static function record(string $action, string $description, array $data = [], $actor = null): ?self
    {
        $actor = $actor ?: auth()->user();

        if (! $actor || ! in_array($actor->role, self::TRACKED_ROLES, true)) {
            return null;
        }

        try {
            return static::create([
                'user_id'      => $actor->id,
                'user_name'    => $actor->name,
                'user_role'    => $actor->role,
                'action'       => $action,
                'description'  => Str::limit($description, 250, '...'),
                'subject_type' => $data['subject_type'] ?? null,
                'subject_id'   => $data['subject_id'] ?? null,
                'properties'   => $data['properties'] ?? null,
                'ip_address'   => request()->ip(),
                'user_agent'   => Str::limit((string) request()->userAgent(), 250, ''),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
