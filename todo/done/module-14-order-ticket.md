# Module 14 : Order Tiket

Module ini bertujuan untuk menangani **proses pemesanan tiket**, mulai dari:

- Menyimpan order
- Mengurangi stok tiket
- Menampilkan riwayat pembelian
- Menampilkan detail pesanan user

---

## **Membuat Controller**

```bash
php artisan make:controller User/OrderController.php
```

Edit file: `app/Http/Controllers/User/OrderController.php`

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
  {
    $user = Auth::user() ?? \App\Models\User::first();
    $orders = Order::where('user_id', $user->id)->with('event')->orderBy('created_at', 'desc')->get();
    
    return view('orders.index', compact('orders'));
  }

  // show a specific order
  public function show(Order $order)
  {
    $order->load('detailOrders.tiket', 'event');
    return view('orders.show', compact('order'));
  }

  // store an order (AJAX POST)
  public function store(Request $request)
  {

    $data = $request->validate([
      'event_id' => 'required|exists:events,id',
      'items' => 'required|array|min:1',
      'items.*.tiket_id' => 'required|integer|exists:tikets,id',
      'items.*.jumlah' => 'required|integer|min:1',
    ]);

    $user = Auth::user();

    try {
      // transaction
      $order = DB::transaction(function () use ($data, $user) {
        $total = 0;
        // validate stock and calculate total
        foreach ($data['items'] as $it) {
          $t = Tiket::lockForUpdate()->findOrFail($it['tiket_id']);
          if ($t->stok < $it['jumlah']) {
            throw new \Exception("Stok tidak cukup untuk tipe: {$t->tipe}");
          }
          $total += ($t->harga ?? 0) * $it['jumlah'];
        }

        $order = Order::create([
          'user_id' => $user->id,
          'event_id' => $data['event_id'],
          'order_date' => Carbon::now(),
          'total_harga' => $total,
        ]);

        foreach ($data['items'] as $it) {
          $t = Tiket::findOrFail($it['tiket_id']);
          $subtotal = ($t->harga ?? 0) * $it['jumlah'];
          DetailOrder::create([
            'order_id' => $order->id,
            'tiket_id' => $t->id,
            'jumlah' => $it['jumlah'],
            'subtotal_harga' => $subtotal,
          ]);

          // reduce stock
          $t->stok = max(0, $t->stok - $it['jumlah']);
          $t->save();
        }

        return $order;
      });

      // flash success message to session so it appears after redirect
      session()->flash('success', 'Pesanan berhasil dibuat.');

      return response()->json(['ok' => true, 'order_id' => $order->id, 'redirect' => route('orders.index')]);
    } catch (\Exception $e) {
      return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
    }
  }
}

```

1. Function `index()`
    
    ```php
    public function index()
    {
    	$user = Auth::user() ?? \App\Models\User::first();
    	$orders = Order::where('user_id', $user->id)
    	->with('event')
    	->orderBy('created_at', 'desc')
    	->get();
    	return view('orders.index', compact('orders'));
    }
    ```
    
    **Penjelasan:**
    
    - Mengambil user yang sedang login menggunakan `Auth::user()`
    - Mengambil seluruh data **order milik user tersebut**
    - Relasi `event` ikut dimuat agar informasi event bisa langsung ditampilkan
    - Data diurutkan dari pesanan terbaru
    - Dikirim ke view `orders.index` untuk ditampilkan sebagai **riwayat pembelian**
2. Function `show(Order $order)`
    
    ```php
    public function show(Order $order)
    {
    	$order->load('detailOrders.tiket', 'event');
    	return view('orders.show', compact('order'));
    }
    ```
    
    **Penjelasan:**
    
    - Menggunakan **Route Model Binding**
    - Memuat:
        - Detail order (`detailOrders`)
        - Relasi tiket dari detail order
        - Data event
    - Data dikirim ke view `orders.show` untuk menampilkan **detail pemesanan**
3. Function `store(Request $request)` 
    
    **Penjelasan Umum:**
    
    Function ini bertugas untuk:
    
    - Menerima data checkout dari frontend (AJAX)
    - Validasi input
    - Mengecek stok tiket
    - Membuat order dan detail order
    - Mengurangi stok tiket
    - Menggunakan **Database Transaction** agar data tetap konsisten

## **Setup Route Dashboard**

Edit file `routes/web.php`, tambahkan route untuk order event:

```php
use App\Http\Controllers\User\OrderController;

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
```

sehingga isi file `routes/web.php` 

![image.png](attachment:e86e5487-0cfc-46b4-989e-26221c33985a:image.png)

## **Buat View Detail Event**

Buat 2 file `index.blade.php` dan `show.blade.php` di lokasi berikut:

```
resources/views/orders
└── index.blade.php
└── show.blade.php
```

1. `index.blade.php`
    
    ```php
    <x-layouts.app>
      <section class="max-w-6xl mx-auto py-12 px-6">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold">Riwayat Pembelian</h1>
        </div>
    
        <div class="space-y-4">
          @forelse($orders as $order)
            <article class="card lg:card-side bg-base-100 shadow-md overflow-hidden">
              <figure class="lg:w-48">
                <img
                  src="{{ $order->event?->gambar ? asset($order->event->gambar) : 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp' }}"
                  alt="{{ $order->event?->judul ?? 'Event' }}" class="w-full h-full object-cover" />
              </figure>
    
              <div class="card-body flex justify-between ">
                <div>
                  <div class="font-bold">Order #{{ $order->id }}</div>
                  <div class="text-sm text-gray-500 mt-1">{{ $order->order_date->translatedFormat('d F Y, H:i') }}</div>
                  <div class="text-sm mt-2">{{ $order->event?->judul ?? 'Event' }}</div>
                </div>
    
                <div class="text-right">
                  <div class="font-bold text-lg">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                  <a href="{{ route('orders.show', $order) }}" class="btn btn-primary mt-3 text-white">Lihat Detail</a>
                </div>
              </div>
            </article>
          @empty
            <div class="alert alert-info">Anda belum memiliki pesanan.</div>
          @endforelse
        </div>
      </section>
    </x-layouts.app>
    ```
    
    View ini digunakan untuk **menampilkan daftar seluruh pesanan** yang pernah dilakukan oleh user yang sedang login.
    
2. `show.blade.php`
    
    ```php
    <x-layouts.app>
      <section class="max-w-4xl mx-auto py-12 px-6">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold">Detail Pemesanan</h1>
          <div class="text-sm text-gray-500">Order #{{ $order->id }} •
            {{ $order->order_date->translatedFormat('d F Y, H:i') }}
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
            </div>
          </div>
        </div>
        <div class="mt-6">
          <a href="{{ route('orders.index') }}" class="btn btn-primary text-white">Kembali ke Riwayat Pembelian</a>
        </div>
      </section>
    </x-layouts.app>
    ```
    
    View ini digunakan untuk **menampilkan detail lengkap dari satu pesanan**.
    

## Ubah Detail Event

Tambahkan code ini pada `resources\views\events\show.blade.php` 

```php
<script>
// code script lain

document.getElementById('confirmCheckout').addEventListener('click', async () => {
      const btn = document.getElementById('confirmCheckout');
      btn.setAttribute('disabled', 'disabled');
      btn.textContent = 'Memproses...';

      // gather items
      const items = [];
      Object.values(tickets).forEach(t => {
        const qty = Number(document.getElementById('qty-' + t.id).value || 0);
        if (qty > 0) items.push({ tiket_id: t.id, jumlah: qty });
      });

      if (items.length === 0) {
        alert('Tidak ada tiket dipilih');
        btn.removeAttribute('disabled');
        btn.textContent = 'Konfirmasi (placeholder)';
        return;
      }

      try {
        const res = await fetch("{{ route('orders.store') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ event_id: {{ $event->id }}, items })
        });

        if (!res.ok) {
          const text = await res.text();
          throw new Error(text || 'Gagal membuat pesanan');
        }

        const data = await res.json();
        // redirect to orders list
        window.location.href = data.redirect || '{{ route('orders.index') }}';
      } catch (err) {
        console.log(err);
        alert('Terjadi kesalahan saat memproses pesanan: ' + err.message);
        btn.removeAttribute('disabled');
        btn.textContent = 'Konfirmasi (placeholder)';
      }
    });
    
 </script>
```

Berfungsi untuk **menghubungkan halaman detail event dengan proses pembuatan order (OrderController@store)** menggunakan **AJAX (fetch API)**.

```php
fetch("{{ route('orders.store') }}", {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': ...
  },
  body: JSON.stringify({ event_id: {{ $event->id }}, items })
});
```

Penjelasan:

- Mengirim data ke `OrderController@store`
- Menggunakan **CSRF token Laravel**
- Data dikirim dalam format JSON
- Tidak reload halaman

## Ubah Navigation

Edit `resources\views\components\user\navigation.blade.php` 

```php
<ul tabindex="0" class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-base-100 rounded-box w-52">
	<li>
		// edit bagian ini
		<a href="#" class="justify-between">
			Riwayat Pembelian
		</a>
	</li>
	<li>
		<a href="{{ route('logout') }}"
			onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
				Logout
		</a>
		<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
			@csrf
		</form>
	</li>
</ul>
```

Tambahkan `{{ route('orders.index') }}`
Sehingga menjadi seperti ini 

```php
<li>
		<a href="{{ route('orders.index') }}" class="justify-between">
			Riwayat Pembelian
		</a>
	</li>
```

# **Testing Fitur Order Tiket**

Lakukan pengujian dengan langkah berikut:

---

### **1. Akses Detail Event**

- Buka halaman homepage
- Klik salah satu event
- Pastikan halaman detail event tampil dengan:
    - Gambar
    - Deskripsi
    - Daftar tiket

### **2. Coba Pilih Tiket**

- Klik tombol `+` untuk menambah jumlah tiket
- Pastikan:
    - Subtotal per tiket berubah
    - Ringkasan pembelian ter-update
    - Tombol **Checkout aktif**

### **3. Cek Riwayat Pembelian**

- Pastikan order baru muncul
- Total harga sesuai
- Event sesuai

### **4. Cek Detail Order**

- Klik **Lihat Detail**
- Pastikan:
    - Tiket tampil sesuai
    - Qty dan subtotal benar
    - Total sesuai

### **5. Validasi Stok**

- Coba beli tiket hingga stok habis
- Pastikan:
    - Tidak bisa melebihi stok
    - Error muncul jika stok tidak cukup