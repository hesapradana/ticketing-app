# Module 7: Admin Middleware

Untuk membatasi akses halaman tertentu agar **hanya dapat diakses oleh pengguna dengan role `admin`**, diperlukan sebuah **middleware khusus**.

**Middleware** adalah komponen di Laravel yang berfungsi sebagai **filter atau perantara** antara *request* yang dikirim oleh pengguna dan *response* yang dikembalikan oleh aplikasi.

Middleware berjalan **sebelum request masuk ke Controller**, sehingga sangat cocok digunakan untuk:

- Autentikasi (login / belum login)
- Otorisasi (hak akses berdasarkan role)
- Proteksi halaman penting (admin, dashboard, dll)

Secara sederhana, middleware bekerja dengan cara:

- Menerima request dari user
- Melakukan pengecekan tertentu
- Menentukan apakah request **boleh dilanjutkan** atau **harus dihentikan**

---

### Membuat Middleware

Jalankan perintah berikut di terminal:

```php
php artisan make:middleware AdminMiddleware
```

Perintah ini akan membuat file middleware baru di `app/Http/Middleware/AdminMiddleware.php`

![image.png](attachment:8ad10641-bace-4c74-b681-1cd4bfc95302:image.png)

## Implementasi AdminMiddleware

Edit file `app/Http/Middleware/AdminMiddleware.php` menjadi seperti berikut:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // If not admin, log out the user
        auth()->logout();
        return redirect('/login');
    }
}
```

## Registrasi Middleware Admin

Agar middleware dapat digunakan di dalam route, middleware perlu **didaftarkan (register)** ke dalam aplikasi Laravel.

Buka file `bootstrap/app.php` dan tambahkan alias middleware `admin` pada konfigurasi middleware

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

sehingga file `bootstrap/app.php` isinya

![image.png](attachment:850ae7b7-48ec-4b3a-aa76-f615ef98bf61:image.png)

## Mengubah Route

Edit file `routes/web.php`.

Hapus konfigurasi lama:

```php
Route::prefix('admin')->group(function () {
	Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
```

Ganti dengan route yang dilindungi middleware:

```php
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
```

Sehingga isi dari `routes/web.php`

![image.png](attachment:e8840125-dc10-49d5-81ae-295b4cacaf1a:image.png)

### Penjelasan

- `middleware('admin')`
    
    Menjamin hanya admin yang bisa mengakses route ini
    
- `prefix('admin')`
    
    Semua URL diawali dengan `/admin`
    
- `name('admin.')`
    
    Memberi penamaan route yang lebih terstruktur
    

## Mengarahkan Login sesuai role

Agar setelah login user diarahkan ke halaman sesuai role, edit file: `app\Http\Controllers\Auth\AuthenticatedSessionController.php`. 
Pada method `store`, ubah menjadi seperti berikut:

```php
public function store(LoginRequest $request): RedirectResponse
{
		$request->authenticate();
		$request->session()->regenerate();
		
		// Redirect based on user role
		$user = Auth::user();
		if ($user->role === 'admin') {
				return redirect()->intended('/admin');
		} else {
				return redirect()->intended('/');
		}
 }
```

sehingga isinya seperti ini

![image.png](attachment:4beef9b8-0093-4278-bd8b-46ad20f0db1b:image.png)

(kasi penjelasan)

## Testing Admin Middleware

Lakukan pengujian dengan langkah berikut:

### 1. Login Menggunakan Akun Seeder

**Admin**

- Email: `admin@gmail.com`
- Password: `password`

**User**

- Email: `user@gmail.com`
- Password: `password`

---

### 2. Verifikasi Redirect Otomatis

**Admin**

Akan diarahkan ke halaman admin dashboard.

![image.png](attachment:de794e4d-a46b-4277-bda3-d4c612bbf86d:image.png)

**User**

Akan diarahkan ke halaman dashboard user.

![image.png](attachment:831185de-e29d-481f-ac8f-a08b787b8e90:image.png)

---

### 3. Uji Proteksi Route Admin

- Login menggunakan akun **user**
- Akses URL `/admin`
- Sistem akan otomatis:
    - Logout user
    - Mengarahkan kembali ke halaman login

Hal ini menandakan **Admin Middleware bekerja dengan baik**.