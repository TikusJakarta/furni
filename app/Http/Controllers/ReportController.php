<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Laporan penjualan: tampil sebagai tabel di halaman admin, dan query yang sama
 * dipakai untuk diunduh sebagai file Excel (PhpOffice/PhpSpreadsheet).
 * Simpan di: app/Http/Controllers/ReportController.php
 */
class ReportController extends Controller
{
    private const PAID_STATUSES = ['paid', 'packing', 'shipping', 'shipped', 'completed'];

    private const STATUSES = [
        'pending'    => ['Belum Bayar',      'status-unpaid'],
        'verifikasi' => ['Verifikasi',       'status-verify'],
        'paid'       => ['Bayar',            'status-paid'],
        'packing'    => ['Kemas Barang',     'status-process'],
        'shipping'   => ['Menentukan Kurir', 'status-process'],
        'shipped'    => ['Dikirim',          'status-shipped'],
        'completed'  => ['Selesai',          'status-completed'],
        'cancelled'  => ['Dibatalkan',       'status-other'],
    ];

    // Halaman laporan (tabel di layar)
    public function sales(Request $request)
    {
        [$from, $to, $status] = $this->filters($request);

        $query = $this->ordersQuery($from, $to, $status);

        $orders = (clone $query)
            ->with('user')
            ->withSum('orderItems as items_qty', 'quantity')
            ->latest()
            ->paginate(20)
            ->onEachSide(1)
            ->withQueryString();

        $summary  = $this->summary($query);
        $products = $this->productsSold($query);

        return view('admin.reports.sales', [
            'orders'      => $orders,
            'summary'     => $summary,
            'products'    => $products,
            'from'        => $from,
            'to'          => $to,
            'status'      => $status,
            'statusMeta'  => self::STATUSES,
            'statusLabel' => $this->statusLabel($status),
        ]);
    }

    // Unduh Excel: memakai filter dan query yang sama dengan halaman di atas
    public function salesExport(Request $request)
    {
        [$from, $to, $status] = $this->filters($request);

        $query = $this->ordersQuery($from, $to, $status);

        $orders = (clone $query)
            ->with('user')
            ->withSum('orderItems as items_qty', 'quantity')
            ->orderBy('created_at')
            ->get();

        $summary  = $this->summary($query);
        $products = $this->productsSold($query);

        $spreadsheet = $this->buildWorkbook($orders, $products, $summary, $from, $to, $this->statusLabel($status));

        $filename = 'laporan-penjualan_' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');

            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function filters(Request $request): array
    {
        $request->validate([
            'from'   => 'nullable|date',
            'to'     => 'nullable|date|after_or_equal:from',
            'status' => 'nullable|in:all,paid_group,' . implode(',', array_keys(self::STATUSES)),
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $status = $request->filled('status') ? $request->status : 'paid_group';

        return [$from, $to, $status];
    }

    private function ordersQuery(Carbon $from, Carbon $to, string $status)
    {
        return Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($status === 'paid_group', function ($query) {
                $query->whereIn('status', self::PAID_STATUSES);
            })
            ->when(! in_array($status, ['all', 'paid_group'], true), function ($query) use ($status) {
                $query->where('status', $status);
            });
    }

    /** Angka ringkasan untuk kartu di atas tabel */
    private function summary($ordersQuery): array
    {
        $itemTable = (new Order)->items()->getRelated()->getTable();

        $orders  = (clone $ordersQuery)->count();
        $revenue = (clone $ordersQuery)->sum('total_price');

        return [
            'orders'   => $orders,
            'revenue'  => $revenue,
            'shipping' => (clone $ordersQuery)->sum('shipping_cost'),
            'average'  => $orders > 0 ? $revenue / $orders : 0,
            'items'    => DB::table($itemTable)
                ->whereIn('order_id', (clone $ordersQuery)->select('id'))
                ->sum('quantity'),
        ];
    }

    private function productsSold($ordersQuery)
    {
        $items    = (new Order)->items()->getRelated()->getTable();
        $products = (new Product)->getTable();

        $price = Schema::hasColumn($items, 'price') ? "$items.price" : "$products.price";

        return DB::table($items)
            ->join($products, "$products.id", '=', "$items.product_id")
            ->whereIn("$items.order_id", (clone $ordersQuery)->select('id'))
            ->selectRaw("$products.id, $products.name, SUM($items.quantity) as qty, SUM($items.quantity * $price) as revenue")
            ->groupBy("$products.id", "$products.name")
            ->orderByDesc('qty')
            ->get();
    }

    private function statusLabel(string $status): string
    {
        if ($status === 'all') {
            return 'Semua status';
        }

        if ($status === 'paid_group') {
            return 'Sudah dibayar';
        }

        return self::STATUSES[$status][0] ?? $status;
    }

    // File Excel
    private function buildWorkbook($orders, $products, array $summary, Carbon $from, Carbon $to, string $statusLabel): Spreadsheet
    {
        $company = \App\Models\Setting::where('key', 'invoice_company')->value('value')
            ?: (\App\Models\Setting::where('key', 'brand_name')->value('value') ?: 'Furni');

        $period = $from->format('d M Y') . ' - ' . $to->format('d M Y');
        $money  = '"Rp" #,##0';

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Penjualan');

        $sheet->setCellValue('A1', 'Laporan Penjualan - ' . $company);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A2', 'Periode: ' . $period);
        $sheet->setCellValue('A3', 'Status: ' . $statusLabel);
        $sheet->setCellValue('A4', 'Dibuat: ' . now()->format('d M Y H:i') . ' oleh ' . optional(auth()->user())->name);

        // Ringkasan
        $summaryRows = [
            ['Jumlah pesanan',        $summary['orders'],   '#,##0'],
            ['Total pendapatan',      $summary['revenue'],  $money],
            ['Total ongkos kirim',    $summary['shipping'], $money],
            ['Rata-rata per pesanan', $summary['average'],  $money],
            ['Item terjual',          $summary['items'],    '#,##0'],
        ];

        $row = 6;
        foreach ($summaryRows as [$label, $value, $format]) {
            $sheet->setCellValue("A$row", $label);
            $sheet->mergeCells("A$row:C$row");
            $sheet->setCellValue("D$row", $value);
            $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode($format);
            $sheet->getStyle("D$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("A$row")->getFont()->setBold(true);
            $row++;
        }

        // Tabel pesanan
        $header = $row + 1;
        $columns = ['No', 'Tanggal', 'No. Pesanan', 'Pelanggan', 'Item', 'Ongkir', 'Total', 'Status'];

        foreach ($columns as $i => $title) {
            $sheet->setCellValue(chr(65 + $i) . $header, $title);
        }

        $this->styleHeader($sheet, "A$header:H$header");

        $first = $header + 1;
        $r = $first;

        foreach ($orders as $i => $order) {
            $customer = trim($order->first_name . ' ' . $order->last_name) ?: (optional($order->user)->name ?? '-');

            $sheet->setCellValue("A$r", $i + 1);
            $sheet->setCellValue("B$r", ExcelDate::PHPToExcel($order->created_at));
            $sheet->setCellValue("C$r", '#' . $order->id);
            $sheet->setCellValue("D$r", $customer);
            $sheet->setCellValue("E$r", (int) $order->items_qty);
            $sheet->setCellValue("F$r", (float) $order->shipping_cost);
            $sheet->setCellValue("G$r", (float) $order->total_price);
            $sheet->setCellValue("H$r", self::STATUSES[$order->status][0] ?? $order->status);
            $r++;
        }

        $last = $r - 1;

        if ($orders->isEmpty()) {
            $sheet->setCellValue("A$first", 'Tidak ada pesanan pada periode ini.');
            $sheet->mergeCells("A$first:H$first");
            $last = $first;
        } else {
            $sheet->getStyle("B$first:B$last")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
            $sheet->getStyle("F$first:G$last")->getNumberFormat()->setFormatCode($money);
            $sheet->getStyle("A$first:A$last")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E$first:E$last")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Baris total 
            $total = $last + 1;
            $sheet->setCellValue("D$total", 'TOTAL');
            $sheet->setCellValue("E$total", "=SUM(E$first:E$last)");
            $sheet->setCellValue("F$total", "=SUM(F$first:F$last)");
            $sheet->setCellValue("G$total", "=SUM(G$first:G$last)");
            $sheet->getStyle("F$total:G$total")->getNumberFormat()->setFormatCode($money);
            $sheet->getStyle("E$total")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A$total:H$total")->applyFromArray([
                'font'    => ['bold' => true],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '3B5D50']]],
            ]);

            $sheet->setAutoFilter("A$header:H$last");
        }

        $this->styleBody($sheet, "A$first:H$last");

        foreach (['A' => 6, 'B' => 18, 'C' => 14, 'D' => 30, 'E' => 8, 'F' => 18, 'G' => 20, 'H' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->freezePane('A' . $first);

        // ====== PRODUK TERJUAL =======
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Produk Terjual');

        $sheet2->setCellValue('A1', 'Produk Terjual - ' . $company);
        $sheet2->mergeCells('A1:D1');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet2->setCellValue('A2', 'Periode: ' . $period . ' | Status: ' . $statusLabel);

        $h2 = 4;
        foreach (['No', 'Produk', 'Jumlah Terjual', 'Pendapatan'] as $i => $title) {
            $sheet2->setCellValue(chr(65 + $i) . $h2, $title);
        }
        $this->styleHeader($sheet2, "A$h2:D$h2");

        $f2 = $h2 + 1;
        $r2 = $f2;

        foreach ($products as $i => $product) {
            $sheet2->setCellValue("A$r2", $i + 1);
            $sheet2->setCellValue("B$r2", $product->name);
            $sheet2->setCellValue("C$r2", (int) $product->qty);
            $sheet2->setCellValue("D$r2", (float) $product->revenue);
            $r2++;
        }

        $l2 = $r2 - 1;

        if ($products->isEmpty()) {
            $sheet2->setCellValue("A$f2", 'Tidak ada produk terjual pada periode ini.');
            $sheet2->mergeCells("A$f2:D$f2");
            $l2 = $f2;
        } else {
            $sheet2->getStyle("D$f2:D$l2")->getNumberFormat()->setFormatCode($money);
            $sheet2->getStyle("A$f2:A$l2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("C$f2:C$l2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $t2 = $l2 + 1;
            $sheet2->setCellValue("B$t2", 'TOTAL');
            $sheet2->setCellValue("C$t2", "=SUM(C$f2:C$l2)");
            $sheet2->setCellValue("D$t2", "=SUM(D$f2:D$l2)");
            $sheet2->getStyle("D$t2")->getNumberFormat()->setFormatCode($money);
            $sheet2->getStyle("C$t2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet2->getStyle("A$t2:D$t2")->applyFromArray([
                'font'    => ['bold' => true],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '3B5D50']]],
            ]);
        }

        $this->styleBody($sheet2, "A$f2:D$l2");

        foreach (['A' => 6, 'B' => 36, 'C' => 16, 'D' => 20] as $col => $width) {
            $sheet2->getColumnDimension($col)->setWidth($width);
        }

        $sheet2->freezePane('A' . $f2);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B5D50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    private function styleBody($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E3E9E6']]],
        ]);
    }
}
