# Module 12: Homepage Pembeli

Pada modul ini kita akan membangun **halaman homepage BengTix**, yaitu halaman utama yang **dapat diakses oleh user tanpa login (guest)**.

Homepage ini menjadi pintu masuk aplikasi dan menampilkan daftar event yang tersedia beserta kategorinya.

## Menyiapkan Aset Gambar

![logo_bengkod.svg](attachment:c87ab569-34da-455c-a103-3bebdaeb3098:logo_bengkod.svg)

**Logo Aplikasi**
Buat folder baru

```php
public/assets/images
```

Masukkan file logo aplikasi ke folder tersebut

```php
logo_bengkod.svg
```

Folder `public` digunakan untuk **aset statis**, artinya file di dalamnya bisa langsung diakses oleh browser tanpa melalui proses backend Laravel.

## Menyiapkan Layout App

Karena homepage dapat diakses oleh user yang belum login, kita menggunakan layout khusus app.

Buat File `app.blade.php` di :

```php
resources\views\components\layouts
└── app.blade.php

```

isi `app.blade.php`

```php
	<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
		    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('components.user.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
	
```

## Blade Component

Pada bagian ini kita akan membahas konsep Blade Component, bagaimana cara menggunakan props, serta alasan kenapa komponen sangat penting dalam pengembangan aplikasi Laravel modern.

Blade Component adalah potongan UI (User Interface) yang:

- Dapat digunakan kembali (reusable)
- Memiliki struktur HTML sendiri
- Bisa menerima data (props)
- Membuat kode lebih rapi dan terorganisir

**Kenapa Harus Menggunakan Component?**

### **Tanpa Component**

Jika kita tidak menggunakan component, kode seperti tombol biasanya akan ditulis berulang:

```
<button class="btn btn-sm btn-primary">Semua</button>
<button class="btn btn-sm btn-outline">Music</button>
<button class="btn btn-sm btn-outline">Sport</button>
```

Masalah yang muncul:

- Kode duplikat
- Sulit diubah (jika style berubah, harus edit banyak file)
- Tidak konsisten antar halaman

### Dengan Component

Dengan component:

- Struktur HTML hanya ditulis **sekali**
- Bisa menerima data dinamis
- Perubahan cukup dilakukan di satu tempat

Contoh pemanggilan:

```
<x-ui.button-custom label="Semua" :active="true" />
<x-ui.button-custom label="Music" />
<x-ui.button-custom label="Sport" />
```

Kode jadi:

- Lebih bersih
- Lebih mudah dibaca
- Lebih scalable

### Kode Component

```php
// button-custom

@props(['active' => false, 'label' => ''])

<button {{ $attributes->merge([
  'class' => 'btn btn-sm rounded-full px-6 normal-case font-medium transition-all ' .
    ($active
      ? 'btn-primary border-blue-900 bg-blue-900 text-white hover:bg-blue-800'
      : 'btn-outline border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white')
]) }}>
  {{ $label }}
</button>
```

1. **@props**
    
    ```php
    @props(['active' => false, 'label' => ''])
    ```
    
    Fungsi:
    
    - Mendefinisikan properti yang bisa diterima component
    - Memberikan nilai default
2. {{ $attributes }}
    
    ```php
    <button {{ $attributes->merge([...]) }}>
    ```
    
    `$attributes` memungkinkan:
    
    - Menambahkan atribut tambahan dari luar component
    - Seperti `id`, `type`, `onclick`, dll

## Membuat Komponen Homepage

1. Komponen Navbar
    
    Buat file `navigation.blade.php` di : 
    
    ```php
    resources\views\components\user
    └── navigation.blade.php
    ```
    
    ini dibuat untuk membuat navbar yang nantinya akan tampil pada halamanan home
    
    Isi dari `navigation.blade.php` :
    
    ```php
    <div class="navbar bg-base-100 shadow-sm">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                    </svg>
                </div>
            </div>
            <a href="{{ route('home') }}">
                <img src={{ asset('assets/images/logo_bengkod.svg') }} />
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <input class="input w-72" placeholder="Cari Event..." />
        </div>
        <div class="navbar-end gap-2">
            <!-- check user session -->
            @guest
                <a href="{{ route('login') }}" class="btn bg-blue-900 text-white">Login</a>
                <a href="{{ route('register') }}" class="btn text-blue-900">Register</a>
            @endguest
    
            @auth
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost rounded-btn">
                        Halo, {{ Auth::user()->name }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
    
                    </div>
                    <ul tabindex="0" class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-base-100 rounded-box w-52">
                        <li>
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
                </div>
            @endauth
    
        </div>
    </div>
    ```
    
    Navbar menampilkan konten berbeda berdasarkan **session login user**. Laravel menyediakan directive Blade bawaan untuk kondisi ini.
    
    ```php
    @guest
    	 <a href="{{ route('login') }}" class="btn bg-blue-900 text-white">Login</a>
    	 <a href="{{ route('register') }}" class="btn text-blue-900">Register</a>
    @endguest
    
    ```
    
    Penjelasan:
    
    - `@guest` akan aktif jika user belum login
    - Digunakan untuk menampilkan:
        - Tombol Login
        - Tombol Register
    
    ```
    @auth
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost rounded-btn">
                        Halo, {{ Auth::user()->name }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
    
                    </div>
                    <ul tabindex="0" class="mt-3 p-2 shadow menu menu-compact dropdown-content bg-base-100 rounded-box w-52">
                        <li>
                            <a href="{{ route('orders.index') }}" class="justify-between">
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
                </div>
            @endauth
    ```
    
    Penjelasan:
    
    - `@auth` aktif jika user sudah login
    - Menampilkan:
        - Nama user dari session (`Auth::user()->name`)
        - Menu dropdown akun
        - Link Riwayat Pembelian
        - Aksi Logout
2. Event Card
    
    Komponen **Event Card** digunakan untuk menampilkan informasi singkat sebuah event pada halaman homepage, seperti:
    
    - Gambar event
    - Judul event
    - Tanggal & waktu
    - Lokasi
    - Harga tiket
    
    Agar tampilan event konsisten dan mudah digunakan ulang, maka Event Card dibuat sebagai **Blade Component**.
    
    ## **Struktur File**
    
    Buat file `event-card.blade.php` di lokasi berikut:
    
    ```
    resources/views/components/user
    └── event-card.blade.php
    ```
    
    Isi dari `event-card.blade.php` :
    
    ```php
    @props(['title', 'date', 'location', 'price', 'image', 'href' => null])
    
    @php
    // Format Indonesian price
    $formattedPrice = $price ? 'Rp ' . number_format($price, 0, ',', '.') : 'Harga tidak tersedia';
    
    $formattedDate = $date
    ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y, H:i')
    : 'Tanggal tidak tersedia';
    
    // Safe image URL: use external URL if provided, otherwise use asset (storage path)
    $imageUrl = $image
    ? (filter_var($image, FILTER_VALIDATE_URL)
    ? $image
    : asset('images/events/' . $image))
    : asset('images/konser.jpeg');
    
    @endphp
    
    <a href="{{ $href ?? '#' }}" class="block">
        <div class="card bg-base-100 h-96 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="h-48 overflow-hidden bg-gray-100 rounded-t-lg flex items-center justify-center">
                <img 
                    src="{{ $imageUrl }}" 
                    alt="{{ $title }}" 
                    class="max-w-full max-h-full object-contain"
                >
            </div>
    
            <div class="card-body">
                <h2 class="card-title">
                    {{ $title }}
                </h2>
    
                <p class="text-sm text-gray-500">
                    {{ $formattedDate }}
                </p>
    
                <p class="text-sm">
                    📍 {{ $location }}
                </p>
    
                <p class="font-bold text-lg mt-2">
                    {{ $formattedPrice }}
                </p>
    
            </div>
        </div>
    </a>
    ```
    
    ## **Konsep Props pada Event Card**
    
    ```php
    @props(['title','date','location','price','image','href' =>null])
    ```
    
    ### **Penjelasan**
    
    Baris ini mendefinisikan **props**, yaitu data yang dikirim dari halaman pemanggil ke dalam component.
    
    Props yang digunakan:
    
    - `title` → Judul event
    - `date` → Tanggal & waktu event
    - `location` → Lokasi event
    - `price` → Harga tiket
    - `image` → Gambar event
    - `href` → Link ke detail event (opsional)
3. Category Pill 
    
    Category Pill adalah komponen kecil berbentuk tombol (pill) yang digunakan untuk menampilkan kategori event pada halaman homepage.
    Komponen ini biasanya dipakai sebagai filter visual, misalnya untuk menandai kategori yang sedang aktif dipilih oleh user.
    
    Agar tampilannya konsisten dan mudah digunakan berulang, Category Pill dibuat sebagai Blade Component.
    
    Buat file `category-pill.blade.php`di : 
    
    ```php
    resources\views\components\user
    └── category-pill.blade.php
    ```
    
    ini dibuat untuk membuat navbar yang nantinya akan tampil pada halamanan home
    
    Isi dari `category-pill.blade.php` :
    
    ```php
    @props(['active' => false, 'label' => ''])
    
    <button {{ $attributes->merge([
      'class' => 'btn btn-sm rounded-full px-6 normal-case font-medium transition-all ' .
        ($active
          ? '!bg-blue-800 !text-white hover:!bg-blue-800'
          : 'bg-white border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white')
    ]) }}>
      {{ $label }}
    </button>
    
    ```
    
    ## **Konsep Props pada Category Pill**
    
    ```php
    @props(['active' => false, 'label' => ''])
    ```
    
    ### **Penjelasan :**
    
    - `label`
        
        Digunakan untuk menampilkan **nama kategori** pada tombol
        
        Contoh: *Seminar*, *Konser*, *Olahraga*
        
    - `active`
        
        Bertipe boolean (`true / false`)
        
        Digunakan untuk menentukan apakah kategori tersebut **sedang aktif dipilih**
        

## Membuat Tampilan Homepage

Pada bagian ini, kita mulai menyatukan semua komponen yang sudah dibuat sebelumnya (Navbar, Event Card, Category Pill, Layout) menjadi **halaman homepage yang dinamis**.

### **Buat DashboardController**

```bash
php artisan make:controller User/HomeController
```

Edit file `app/Http/Controllers/User/HomeController.php`:

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kategori;
use Illuminate\Http\Request;

class HomeController extends Controller
{
     public function index(Request $request)
    {
        $categories = Kategori::all();

        $eventsQuery = Event::withMin('tikets', 'harga')
            ->orderBy('tanggal_waktu', 'asc');

        if ($request->has('kategori') && $request->kategori) {
            $eventsQuery->where('kategori_id', $request->kategori);
        }

        $events = $eventsQuery->get();

        return view('home', compact('events', 'categories'));
    }
}

```

**Penjelasan Alur Controller**

1. **Mengambil semua kategori**
    
    ```php
    $categories =Kategori::all();
    ```
    
    Data ini digunakan untuk menampilkan daftar kategori pada **Category Pill** di homepage.
    
2. **Membuat query event**
    
    ```php
    Event::withMin('tikets','harga')
    ```
    
    Digunakan untuk mengambil **harga tiket termurah** dari relasi `tikets`, agar bisa langsung ditampilkan di Event Card.
    
3. **Filter berdasarkan kategori**
    
    ```php
    if ($request->has('kategori'))
    ```
    
    Jika user memilih kategori tertentu, event akan difilter berdasarkan `kategori_id`.
    
4. **Kirim data ke view**
    
    ```php
    return view('home',compact('events','categories'));
    ```
    
    Data `events` dan `categories` akan digunakan langsung di Blade.
    

## **Setup Route Dashboard**

Edit file `routes/web.php`, edit route untuk home:

Secara default, Laravel menampilkan halaman `welcome`

Karena homepage sekarang dikelola oleh controller, route perlu diubah.

**Route Awal**

```php
Route::get('/', function () {
    return view('welcome');
});
```

Route Baru

```php
// jangan lupa import controller
use App\Http\Controllers\User\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
```

sehingga isi file `routes/web.php` 

![image.png](attachment:24b995b2-e459-4bf3-bc7f-615b5efe3d60:image.png)

## **Buat View Homepage**

Buat file di lokasi berikut:

```
resources/views
└── home.blade.php
```

Isi dengan code berikut :

```
<x-layouts.app>
    <div class="hero bg-blue-900 min-h-screen">
        <div class="hero-content text-center text-white">
            <div class="max-w-4xl">
                <h1 class="text-5xl font-bold">Hi, Amankan Tiketmu yuk.</h1>
                <p class="py-6">
                    BengTix: Beli tiket, auto asik.
                </p>
            </div>
        </div>
    </div>

    <section class="max-w-7xl mx-auto py-12 px-6">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-black uppercase italic">Event</h2>
            <div class="flex gap-2">
                <a href="{{ route('home') }}">
                    <x-user.category-pill :label="'Semua'" :active="!request('kategori')" />
                </a>
                @foreach($categories as $kategori)
                <a href="{{ route('home', ['kategori' => $kategori->id]) }}">
                    <x-user.category-pill :label="$kategori->nama" :active="request('kategori') == $kategori->id" />
                </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($events as $event)
            <x-user.event-card :title="$event->judul" :date="$event->tanggal_waktu" :location="$event->lokasi"
                :price="$event->tikets_min_harga" :image="$event->gambar" />
            @endforeach
        </div>
    </section>
</x-layouts.app>
```

**Penjelasan :** 

**Menampilkan Kategori (Category Pill)**

```csharp
<div class="flex gap-2">
	<a href="{{ route('home') }}">
		<x-user.category-pill :label="'Semua'" :active="!request('kategori')" />
	</a>
	@foreach($categories as $kategori)
		<a href="{{ route('home', ['kategori' => $kategori->id]) }}">
			<x-user.category-pill :label="$kategori->nama" :active="request('kategori') == $kategori->id" />
		</a>
	@endforeach
</div>
```

### **Penjelasan**

- `label` → teks yang ditampilkan
- `active` → menentukan kategori yang sedang dipilih
- Jika tidak ada parameter `kategori`, maka tombol **`Semua**` aktif
- `@foreach($categories as $kategori)` untuk looping kategori yang diambil sesuai database, sehingga nanti ditampilkan sebagai Category Pill

**Menampilkan Event (Event Card)**

```csharp
@foreach($events as $event)
	<x-user.event-card
	:title="$event->judul"
	:date="$event->tanggal_waktu"
	:location="$event->lokasi"
	:price="$event->tikets_min_harga"
	:image="$event->gambar"
	/>
@endforeach
```

**Penjelasan**

- Setiap event ditampilkan menggunakan **Event Card Component**
- Data dikirim melalui props
- Harga sudah diformat dari controller
- Tampilan event menjadi konsisten dan reusable

## **Testing Homepage**

Lakukan pengujian dengan langkah berikut:

1. Jalankan server dan buka halaman utama:
    
    ```
    http://localhost:8000
    ```
    
2. Pastikan homepage tampil dengan benar (hero section, daftar event, dan kategori).
    
    ![image.png](attachment:6a67423a-0615-4ea0-bf89-55f8a2805bac:image.png)
    
    ![image.png](attachment:4cca0b65-1f2e-4a01-945c-f1a73db9cba8:image.png)
    
3. Tambahkan data event melalui halaman admin.
4. Refresh homepage dan pastikan event yang ditambahkan muncul.
5. Klik kategori dan pastikan event terfilter sesuai kategori yang dipilih.
6. Klik **Semua** untuk menampilkan seluruh event kembali.