<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\LimitStock;
use App\Models\Coupon;
use App\Models\User; // <-- Tambahkan model User di sini
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    // Dashboard Utama Admin
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalRevenue = Order::whereIn('status', ['paid', 'completed'])->sum('total_price');
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('settings', 'totalOrders', 'totalProducts', 'totalRevenue', 'recentOrders'));
    }

    // --- 1. CRUD PRODUK ---
    public function productsIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $products = Product::latest()->get();
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
        $orders = Order::with('user', 'orderItems.product')->latest()->get();
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
        return view('admin.settings.index', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
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
    public function usersIndex()
    {
        $settings = Setting::pluck('value', 'key')->all();
        // Menampilkan semua user kecuali admin yang sedang login
        $users = User::where('id', '!=', auth()->id())->latest()->get();
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
}