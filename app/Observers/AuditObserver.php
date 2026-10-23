<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Mencatat otomatis setiap tambah / ubah / hapus data yang dilakukan admin,
 * lengkap dengan isi sebelum dan sesudahnya. Dipasang di AppServiceProvider.
 * Simpan di: app/Observers/AuditObserver.php
 */
class AuditObserver
{
    /** Nama model => kata yang tampil di log */
    private const LABELS = [
        Product::class     => 'Produk',
        Coupon::class      => 'Kupon',
        Order::class       => 'Pesanan',
        User::class        => 'Akun',
        Setting::class     => 'Pengaturan',
        OrderReturn::class => 'Retur',
    ];

    private const VERBS = [
        'created' => 'Menambah',
        'updated' => 'Mengubah',
        'deleted' => 'Menghapus',
    ];

    /** Kolom yang tidak pernah dicatat isinya (rahasia atau tidak berguna) */
    private const HIDDEN_FIELDS = [
        'id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at', 'email_verified_at',
    ];

    public function created(Model $model): void
    {
        $this->log('created', $model);
    }

    public function updated(Model $model): void
    {
        $this->log('updated', $model);
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model);
    }

    private function log(string $event, Model $model): void
    {
        $actor = auth()->user();

        // Hanya aktivitas admin; tindakan pelanggan (checkout, dll) tidak dicatat
        if (! $actor || ! in_array($actor->role, ActivityLog::TRACKED_ROLES, true)) {
            return;
        }

        // Stok produk berkurang otomatis saat pesanan ditandai "paid": itu efek samping,
        // bukan perubahan yang dilakukan admin pada produk.
        if ($model instanceof Product && Route::currentRouteName() === 'admin.orders.updateStatus') {
            return;
        }

        $changes = $this->changes($event, $model);

        // Tidak ada perubahan yang berarti (mis. hanya updated_at)
        if ($event === 'updated' && $changes === []) {
            return;
        }

        $label = self::LABELS[get_class($model)] ?? class_basename($model);

        if ($model instanceof User && in_array($model->role, ActivityLog::TRACKED_ROLES, true)) {
            $label = 'Akun Admin';
        }

        ActivityLog::record(
            $event,
            self::VERBS[$event] . ' ' . $label . ': ' . $this->name($model),
            [
                'subject_type' => class_basename($model),
                'subject_id'   => $model->getKey(),
                'properties'   => $changes ? ['changes' => $changes] : null,
            ],
            $actor
        );
    }

    /** ['kolom' => ['old' => ..., 'new' => ...]] */
    private function changes(string $event, Model $model): array
    {
        $result = [];

        if ($event === 'updated') {
            foreach ($model->getChanges() as $field => $new) {
                if (in_array($field, self::HIDDEN_FIELDS, true)) {
                    continue;
                }

                $result[$field] = [
                    'old' => $this->clip($model->getOriginal($field)),
                    'new' => $this->clip($new),
                ];
            }

            return $result;
        }

        foreach ($model->getAttributes() as $field => $value) {
            if (in_array($field, self::HIDDEN_FIELDS, true) || $value === null || $value === '') {
                continue;
            }

            $result[$field] = $event === 'created'
                ? ['old' => null, 'new' => $this->clip($value)]
                : ['old' => $this->clip($value), 'new' => null];
        }

        return $result;
    }

    /** Nama yang mudah dikenali: nama, kode, kunci pengaturan, atau #id */
    private function name(Model $model): string
    {
        return (string) ($model->name ?? $model->code ?? $model->key ?? ('#' . $model->getKey()));
    }

    private function clip($value)
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        return is_string($value) ? Str::limit($value, 120) : $value;
    }
}
