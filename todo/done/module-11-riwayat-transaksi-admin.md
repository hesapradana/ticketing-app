# **Module 11: Riwayat Transaksi Admin**

Pada modul ini, kita akan membangun fitur **Manajemen Transaksi** yang digunakan oleh **Admin** untuk memantau seluruh aktivitas pembelian tiket yang dilakukan oleh user.

Fitur ini sangat penting karena:

- Admin dapat melihat semua transaksi yang terjadi
- Admin dapat mengecek detail transaksi untuk kebutuhan monitoring, validasi, atau laporan

---

## **Membuat Controller**

Jalankan perintah berikut:

```bash
php artisan make:controller Admin/HistoriesController
```

Setelah itu, edit file:`app\Http\Controllers\Admin\HistoriesController.php` 

Controller ini akan menangani logika untuk menampilkan **daftar transaksi** dan **detail transaksi**.

1. Function `index`
    
    ```
    public function index()
        {
            $histories = Order::latest()->get();
            return view('admin.history.index', compact('histories'));
        }
    ```
    
2. Function `show`
    
    ```
    public function show(string $history)
        {
            $order = Order::findOrFail($history);
            return view('admin.history.show', compact('order'));
        }    
    ```
    

Sehingga isi dari `HistoriesController` : 

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoriesController extends Controller
{
    public function index()
    {
        $histories = Order::latest()->get();
        return view('admin.history.index', compact('histories'));
    }

    public function show(string $history)
    {
        $order = Order::findOrFail($history);
        return view('admin.history.show', compact('order'));
    }
}

```

## **2. Setup Routes**

Agar halaman history transaksi dapat diakses, kita perlu mendaftarkan route.

Edit file `routes/web.php`

```php
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Event Management
        Route::resource('events', EventController::class);

        // Tiket Management 
        Route::resource('tickets', TiketController::class);
        
        // Histories
        Route::get('/histories', [HistoriesController::class, 'index'])->name('histories.index');
        Route::get('/histories/{id}', [HistoriesController::class, 'show'])->name('histories.show');
    });
```

Tambahkan route history **di dalam group admin middleware** agar:

- Hanya admin yang dapat mengakses
- User biasa tidak bisa membuka halaman ini

### Tujuan Routing Ini

- `/admin/histories` → halaman daftar transaksi
- `/admin/histories/{id}` → halaman detail transaksi

Dengan menggunakan middleware admin, keamanan akses tetap terjaga.

## **Update Sidebar (Tambah Menu History Pembelian)**

Agar admin dapat mengakses menu History Pembelian melalui UI, kita perlu menambahkan menu baru di sidebar.

Edit file `resources/views/components/admin/sidebar.blade.php`

### Penjelasan Menu History Pembelian

Menu ini:

- Ditampilkan khusus di sidebar admin
- Mengarah ke halaman daftar transaksi
- Menggunakan `request()->routeIs()` agar menu aktif otomatis saat dibuka

```php
<!-- History item -->
<li class="{{ request()->routeIs('admin.histories.*') ? 'bg-gray-200 rounded-lg' : '' }}">
	<a href="{{ route('admin.histories.index') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="History">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="12" cy="12" r="10"></circle>
			<polyline points="12 6 12 12 16 14"></polyline>
		</svg>
		<span class="is-drawer-close:hidden">History Pembelian</span>
	</a>
</li>
```

## **Membuat View History Admin (Index)**

Selanjutnya, kita membuat tampilan untuk **daftar transaksi**.

### Struktur Folder

Buat folder dan file berikut:

```
resources/views/admin/history/
└── index.blade.php
└── show.blade.php
```

### Halaman `index.blade.php`

```php
<x-layouts.admin title="History Pembelian">
    <div class="container mx-auto p-10">
        <div class="flex">
            <h1 class="text-3xl font-semibold mb-4">History Pembelian</h1>
        </div>
        <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pembeli</th>
                        <th>Event</th>
                        <th>Tanggal Pembelian</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histories as $index => $history)
                    <tr>
                        <th>{{ $index + 1 }}</th>
                        <td>{{ $history->user->name }}</td>
                        <td>{{ $history->event?->judul ?? '-' }}</td>
                        <td>{{ $history->created_at->format('d M Y') }}</td>
                        <td>{{ number_format($history->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.histories.show', $history->id) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada history pembelian tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

```

Halaman ini digunakan untuk:

- Menampilkan seluruh transaksi pembelian
- Menampilkan data penting secara ringkas
- Memberikan tombol untuk melihat detail transaksi

### Penjelasan Tampilan

Pada tabel history pembelian:

- **Nama Pembeli** → diambil dari relasi user
- **Event** → event yang dibeli
- **Tanggal Pembelian** → waktu transaksi dibuat
- **Total Harga** → total pembayaran
- **Aksi** → tombol menuju halaman detail

Jika data kosong, sistem akan menampilkan pesan:

> “Tidak ada history pembelian tersedia.”
> 

### **Halaman Detail Transaksi**

```php
<x-layouts.admin title="Detail Pemesanan">
  <section class="max-w-4xl mx-auto py-12 px-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Detail Pemesanan</h1>
      <div class="text-sm text-gray-500">Order #{{ $order->id }} •
        {{ $order->order_date->format('d M Y H:i') }}
      </div>
    </div>

    <div class="card bg-base-100 shadow-md">
      <div class="lg:flex ">
        <div class="lg:w-1/3 p-4">
          <img
            src="{{ $order->event?->gambar ? asset($order->event->gambar) : 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp' }}"
            alt="{{ $order->event?->judul ?? 'Event' }}" class="w-full object-cover mb-2" />
          <h2 class="font-semibold text-lg">{{ $order->event?->judul ?? 'Event' }}</h2>
          <p class="text-sm text-gray-500 mt-1">{{ $order->event?->lokasi ?? '' }}</p>
        </div>
        <div class="card-body lg:w-2/3">

          <div class="space-y-3">
            @foreach($order->detailOrders as $d)
              <div class="flex justify-between items-center">
                <div>
                  <div class="font-bold">{{ $d->tiket->tipe }}</div>
                  <div class="text-sm text-gray-500">Qty: {{ $d->jumlah }}</div>
                </div>
                <div class="text-right">
                  <div class="font-bold">Rp {{ number_format($d->subtotal_harga, 0, ',', '.') }}</div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="divider"></div>

          <div class="flex justify-between items-center">
            <span class="font-bold">Total</span>
            <span class="font-bold text-lg">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>

          </div>
          <div class="sm:ml-auto sm:mt-auto sm:mr-0 mx-auto mt-3 flex gap-2">
            <a href="{{ route('admin.histories.index') }}" class="btn btn-primary">Kembali ke Riwayat</a>
          </div>
        </div>
      </div>

    </div>
  </section>
</x-layouts.admin>

```

## Testing Fitur **Riwayat Transaksi Admin**

Lakukan pengujian dengan langkah berikut:

1. Login sebagai **Admin**
2. Akses halaman history pembelian pada sidebar
3. Pastikan halaman muncul data sesuai dengan yang ada di database