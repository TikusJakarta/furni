<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\LimitStock;
use App\Models\Coupon;
use App\Models\User;
use App\Support\ThemeColors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // --- AUTENTIKASI ADMIN ---
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $settings = Setting::pluck('value', 'key')->all();
        return view('admin.auth.login', compact('settings'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = auth()->user();

            // Pastikan hanya role admin yang bisa masuk
            if ($user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Akun ini bukan akun Administrator.',
                ])->withInput();
            }

            // Cek status banned
            if ($user->status == 'banned') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Administrator Anda telah diblokir.',
                ])->withInput();
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password administrator salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Berhasil keluar dari panel admin.');
    }

    // Dashboard Utama Admin
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();

        $paidStatuses = ['paid', 'packing', 'shipping', 'shipped', 'completed'];

        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalRevenue = Order::whereIn('status', $paidStatuses)->sum('total_price');
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // Pelanggan (semua user selain admin; role kosong dihitung sebagai pelanggan)
        $customers = fn() => User::where(function ($q) {
            $q->where('role', '!=', 'admin')->orWhereNull('role');
        });

        $totalCustomers = $customers()->count();
        $newCustomers = $customers()->latest()->take(5)->get();

        // Pendapatan bulan ini vs bulan lalu
        $revenueThisMonth = Order::whereIn('status', $paidStatuses)
            ->whereBetween('created_at', [now()->startOfMonth(), now()])
            ->sum('total_price');

        $lastMonth = now()->subMonthNoOverflow();
        $revenueLastMonth = Order::whereIn('status', $paidStatuses)
            ->whereBetween('created_at', [$lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth()])
            ->sum('total_price');

        // Penjualan 7 hari terakhir (hari tanpa penjualan = 0)
        $salesByDay = Order::whereIn('status', $paidStatuses)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(total_price) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'day');

        $salesChart = collect(range(6, 0))->map(function ($i) use ($salesByDay) {
            $date = now()->subDays($i);

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->translatedFormat('d M'),
                'total' => (int) ($salesByDay[$date->toDateString()] ?? 0),
            ];
        })->values();

        // Jumlah pesanan per status
        $statusCounts = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Perlu tindakan
        $pendingVerification = $statusCounts['verifikasi'] ?? 0;
        $pendingReturns = \App\Models\OrderReturn::where('status', 'pending')->count();
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock')->take(5)->get();

        // Produk terlaris (jumlah terjual dari item pesanan yang sudah dibayar).
        // Nama tabel item diambil dari relasi Order::items(), jadi tidak perlu ditebak.
        $orderTable = (new Order)->getTable();
        $productTable = (new Product)->getTable();
        $itemTable = (new Order)->items()->getRelated()->getTable();

        $topProducts = DB::table($itemTable)
            ->join($orderTable, "$orderTable.id", '=', "$itemTable.order_id")
            ->join($productTable, "$productTable.id", '=', "$itemTable.product_id")
            ->whereIn("$orderTable.status", $paidStatuses)
            ->selectRaw("$productTable.id, $productTable.name, $productTable.image, $productTable.price, SUM($itemTable.quantity) as sold")
            ->groupBy("$productTable.id", "$productTable.name", "$productTable.image", "$productTable.price")
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'settings',
            'totalOrders',
            'totalProducts',
            'totalRevenue',
            'recentOrders',
            'totalCustomers',
            'newCustomers',
            'revenueThisMonth',
            'revenueLastMonth',
            'salesChart',
            'statusCounts',
            'pendingVerification',
            'pendingReturns',
            'lowStockCount',
            'lowStockProducts',
            'topProducts'
        ));
    }


    // --- 1. CRUD PRODUK ---
    public function productsIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $products = Product::latest()->paginate(5)->onEachSide(1);

        return view('admin.products.index', compact('settings', 'products'));
    }


    public function productsCreate()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return view('admin.products.create', compact('settings'));
    }

    public function productsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'type' => 'required|in:main,popular',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'image' => 'images/' . $imageName,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function productsEdit($id)
    {
        $settings = Setting::pluck('value', 'key')->all();
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('settings', 'product'));
    }

    public function productsUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'type' => 'required|in:main,popular',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'image' => $imagePath,
            'description' => $request->description,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function productsDestroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    // --- 2. MANAJEMEN PESANAN ADMIN ---
    public function ordersIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $orders = Order::with('user', 'orderItems.product')->latest()->paginate(10)->onEachSide(1);

        return view('admin.orders.index', compact('settings', 'orders'));
    }


    public function ordersUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,verifikasi,paid,packing,shipping,shipped,completed,cancelled'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with('items')->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;

            if ($newStatus === 'paid' && $oldStatus !== 'paid') {
                foreach ($order->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->stock -= $item->quantity;
                        $product->save();
                    }
                }
                LimitStock::where('order_id', $order->id)->delete();
            }

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                LimitStock::where('order_id', $order->id)->delete();
            }

            $order->status = $newStatus;
            $order->save();

            DB::commit();

            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    // --- 3. CMS PENGATURAN WEB ---
    public function settingsIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $themeGroups = ThemeColors::groups();

        return view('admin.settings.index', compact('settings', 'themeGroups'));
    }


    public function settingsUpdate(Request $request)
    {
        $request->validate(
            ThemeColors::rules(),
            ['regex' => ':attribute harus berupa kode warna 6 digit, contoh #3b5d50.'],
            ThemeColors::attributes()
        );

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan website (CMS) berhasil diperbarui!');
    }


    // --- 4. MANAJEMEN KUPON / PROMO ---
    public function couponsIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('settings', 'coupons'));
    }

    public function couponsCreate()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return view('admin.coupons.create', compact('settings'));
    }

    public function couponsStore(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend ?? 0,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Kode promo berhasil dibuat!');
    }

    public function couponsDestroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Kode promo berhasil dihapus!');
    }

    // --- 5. MANAJEMEN PENGGUNA (USER) ---
    public function usersIndex(Request $request)
    {
        $settings = Setting::pluck('value', 'key')->all();

        $users = User::where('id', '!=', auth()->id())
            ->when(in_array($request->status, ['active', 'suspended', 'banned']), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->get();

        return view('admin.users.index', compact('settings', 'users'));
    }


    public function usersUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,banned'
        ]);

        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return redirect()->back()->with('success', 'Status akun ' . $user->name . ' berhasil diperbarui menjadi ' . strtoupper($user->status) . '!');
    }

    // --- 6. MANAJEMEN RETUR / REFUND ---
    public function returnsIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $returns = \App\Models\OrderReturn::with('user', 'order')->latest()->get();
        return view('admin.returns.index', compact('settings', 'returns'));
    }

    public function returnsUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $return = \App\Models\OrderReturn::findOrFail($id);
        $return->status = $request->status;
        $return->save();

        return redirect()->back()->with('success', 'Status pengajuan retur berhasil diperbarui!');
    }
}