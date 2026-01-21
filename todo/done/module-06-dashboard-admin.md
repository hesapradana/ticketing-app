# Module 6: Dashboard Admin

![image.png](attachment:9bcece3c-8aac-46e4-bf22-8be8d86879ea:image.png)

Setelah proses **authentication** selesai, langkah selanjutnya adalah membuat **Dashboard Admin** sebagai halaman utama yang hanya dapat diakses oleh pengguna dengan role **admin**.

Dashboard ini berfungsi untuk menampilkan **ringkasan data utama** pada aplikasi ticketing, seperti:

- Total Event
- Total Kategori
- Total Transaksi

## **Buat DashboardController**

```bash
php artisan make:controller Admin/DashboardController
```

Edit file `app/Http/Controllers/Admin/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Tiket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $totalEvents = Event::count();
        $totalCategories = \App\Models\Kategori::count();
        $totalOrders = Order::count();
        return view('admin.dashboard', compact('totalEvents', 'totalCategories', 'totalOrders'));
    }
}
```

## **Setup Route Dashboard**

Edit file `routes/web.php`, tambahkan route untuk dashboard admin:

```markdown
use App\Http\Controllers\Admin\DashboardController;

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
});
```

sehingga isi file `routes/web.php` 

![Screenshot 2026-01-07 204059.png](attachment:4808ce13-b1c1-4a36-ab26-2a30d9f6222c:Screenshot_2026-01-07_204059.png)

**Penjelasan:**

- Route hanya bisa diakses oleh user yang sudah login
- Dashboard dapat diakses melalui URL `/admin`

## **Buat View Dashboard**

Buat folder `resources/views/admin/` dan file `dashboard.blade.php`:

```php
<x-layouts.admin title="Dashboard Admin">
    <div class="container mx-auto p-10">
        <h1 class="text-3xl font-semibold mb-4">Dashboard Admin</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">Total Event</h2>
                    <p class="font-bold text-4xl">{{ $totalEvents ?? 0 }}</p>
                </div>
            </div>
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">Kategori</h2>
                    <p class="font-bold text-4xl">{{ $totalCategories ?? 0 }}</p>
                </div>
            </div>
            <div class="card bg-base-100 card-sm shadow-xs p-2">
                <div class="card-body">
                    <h2 class="card-title text-md">Total Transaksi</h2>
                    <p class="font-bold text-4xl">{{ $totalOrders ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>
```

## Mengubah sidebar

edit file `resources\views\components\admin\sidebar.blade.php`

1. Dashboard Item
    
    ```php
    <!-- Dashboard Item -->
    <li class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-200 rounded-lg' : '' }}">
    	<a href="{{ route('admin.dashboard') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Dashboard">
    		<!-- Home icon -->
    		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    			<path fill="currentColor" d="M6 19h3v-5q0-.425.288-.712T10 13h4q.425 0 .713.288T15 14v5h3v-9l-6-4.5L6 10zm-2 0v-9q0-.475.213-.9t.587-.7l6-4.5q.525-.4 1.2-.4t1.2.4l6 4.5q.375.275.588.7T20 10v9q0 .825-.588 1.413T18 21h-4q-.425 0-.712-.288T13 20v-5h-2v5q0 .425-.288.713T10 21H6q-.825 0-1.412-.587T4 19m8-6.75" />
    		</svg>
    		<span class="is-drawer-close:hidden">Dashboard</span>
    	</a>
    /li>
    ```
    
2. Logout
    
    ```
    <!-- logout -->
            <div class="w-full p-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-error w-full is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Logout">
                        <!-- Logout icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M10 17v-2h4v-2h-4v-2l-5 3l5 3m9-12H5q-.825 0-1.413.588T3 7v10q0 .825.587 1.413T5 19h14q.825 0 1.413-.587T21 17v-3h-2v3H5V7h14v3h2V7q0-.825-.587-1.413T19 5z" />
                        </svg>
                        <span class="is-drawer-close:hidden">Logout</span>
                    </button>
                </form>
            </div>
    ```
    

sehingga sidebar menjadi seprti ini

```php
<div class="drawer-side is-drawer-close:overflow-visible ">
    <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
    <div class="flex min-h-full flex-col items-start bg-base-200 w-64 is-drawer-close:w-14 is-drawer-open:w-80">
        <div class="w-full flex items-center justify-center p-4">
            <img src="{{ asset('assets/images/logo_bengkod.svg') }}" alt="Logo">
        </div>

        <!-- Sidebar content here -->
        <ul class="menu w-full grow gap-1">
            <!-- Dashboard Item -->
            <li class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-200 rounded-lg' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Dashboard">
                    <!-- Home icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M6 19h3v-5q0-.425.288-.712T10 13h4q.425 0 .713.288T15 14v5h3v-9l-6-4.5L6 10zm-2 0v-9q0-.475.213-.9t.587-.7l6-4.5q.525-.4 1.2-.4t1.2.4l6 4.5q.375.275.588.7T20 10v9q0 .825-.588 1.413T18 21h-4q-.425 0-.712-.288T13 20v-5h-2v5q0 .425-.288.713T10 21H6q-.825 0-1.412-.587T4 19m8-6.75" />
                    </svg>
                    <span class="is-drawer-close:hidden">Dashboard</span>
                </a>
            </li>

            <!-- Kategori item -->
            <li class="">
                <a href="#" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Kategori">
                    <!-- icon Kategori -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 3a3 3 0 1 0 6 0a3 3 0 1 0-6 0" />
                    </svg>
                    <span class="is-drawer-close:hidden">Manajemen Kategori</span>
                </a>
            </li>

            <!-- Event item -->
            <li class="">
                <a href="#" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Event">
                    <!-- icon Event -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V7a2 2 0 0 1 2-2" />
                    </svg>
                    <span class="is-drawer-close:hidden">Manajemen Event</span>
                </a>
            </li>

            <!-- History item -->
            <li class="{{ request()->routeIs('histories') ? 'bg-gray-200 rounded-lg' : '' }}">
                <a href="#" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="History">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span class="is-drawer-close:hidden">History Pembelian</span>
                </a>
            </li>
        </ul>

        <!-- logout -->
        <div class="w-full p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline btn-error w-full is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Logout">
                    <!-- Logout icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M10 17v-2h4v-2h-4v-2l-5 3l5 3m9-12H5q-.825 0-1.413.588T3 7v10q0 .825.587 1.413T5 19h14q.825 0 1.413-.587T21 17v-3h-2v3H5V7h14v3h2V7q0-.825-.587-1.413T19 5z" />
                    </svg>
                    <span class="is-drawer-close:hidden">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
```

## Testing Dashboard Admin

Lakukan pengujian dengan langkah berikut:

1. Login sebagai admin
**Email:** `admin@gmail.com`  
**Password:** `password`
2. Setelah login, arahkan url ke `/admin`
3. Dashboard tampil dengan layout dan sidebar
    
    ![image.png](attachment:ae21d16f-643b-424e-a957-bc533acccdb6:image.png)
    
4. Klik tombol logout sehingga akan kembali ke halaman awal