# 🛋️ Furni - Online Furniture Marketplace

**Furni** adalah sebuah platform web marketplace khusus furnitur yang dibangun menggunakan **Laravel** dan **MySQL**. Proyek ini dikembangkan sebagai portofolio utama selama masa Praktik Kerja Lapangan (PKL).

---

## 📸 Preview Aplikasi
*(Tips: Rekam layar tokomu atau screenshot halaman utama, lalu upload gambarnya di sini agar klien langsung paham wujud websitemu)*

---

## 🚀 Fitur Utama (Features)

### 👤 Sisi Pengguna (Customer Side)
* **Katalog Produk:** Menampilkan produk furnitur berdasarkan kategori, harga, dan popularitas.
* **Manajemen Keranjang:** Sistem shopping cart yang interaktif untuk menampung produk sebelum checkout.
* **Checkout System:** Proses pemesanan produk yang terintegrasi (bisa ditambahkan detail seperti WhatsApp checkout atau simulasi payment gateway).

### 🔐 Sisi Admin (Admin Panel)
* **Autentikasi Aman:** Sistem login admin yang terlindungi untuk mencegah akses tidak sah.
* **Manajemen Produk (CRUD):** Tambah, lihat, ubah, dan hapus data produk, stok, serta harga langsung dari dasbor.
* **Manajemen Kategori:** Pengelompokan furnitur (misal: Kursi, Meja, Lemari) untuk mempermudah pencarian.

---

## 🛠️ Teknologi yang Digunakan (Tech Stack)

* **Backend Framework:** Laravel (PHP)
* **Database:** MySQL dengan Eloquent ORM (Struktur database relasional yang aman dari SQL Injection)
* **Frontend:** Blade Templating + CSS Framework (isi dengan Tailwind CSS / Bootstrap sesuai yang kamu pakai)

---

## 🗄️ Struktur Database (SQL)
Proyek ini mengelola beberapa tabel relasional penting:
* `users` & `admins` (Menangani data akun dan hak akses)
* `products` (Menangani data furnitur, harga, gambar, dan stok)
* `categories` (Relasi satu-ke-banyak untuk pengelompokan produk)
* `orders` / `carts` (Menangani transaksi transaksi pengguna)

---

## 💻 Cara Menjalankan Proyek Secara Lokal

Jika ingin menguji proyek ini di komputer Anda, ikuti langkah berikut:

1. Clone repositori ini:
   ```bash
   git clone https://github.com
   ```
2. Install dependensi PHP:
   ```bash
   composer install
   ```
3. Salin file konfigurasi env:
   ```bash
   cp .env.example .env
   ```
4. Buat *application key*:
   ```bash
   php artisan key:generate
   ```
5. Atur koneksi database MySQL di file `.env`, lalu jalankan migrasi:
   ```bash
   php artisan migrate
   ```
6. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
