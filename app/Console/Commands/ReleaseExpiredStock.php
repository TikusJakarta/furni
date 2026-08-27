<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LimitStock;

class ReleaseExpiredStock extends Command
{
    protected $signature = 'stock:release-expired';
    protected $description = 'Menghapus reservasi limit stok yang sudah melewati batas waktu expires_at';

    public function handle()
    {
        $expiredCount = LimitStock::where('expires_at', '<=', now())->delete();

        $this->info("Berhasil merilis {$expiredCount} data limit stok yang kadaluarsa.");
    }
}