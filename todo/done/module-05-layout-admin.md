# Module 5: Layout Admin

## Konsep Layout Admin

Layout Admin berfungsi sebagai **kerangka tampilan utama** untuk halaman admin, yang terdiri dari:

- Navbar
- Sidebar
- Area konten utama
- Footer

Setiap halaman admin hanya perlu **mengisi konten**, tanpa menulis ulang struktur HTML yang sama.

## Membuat Layout Admin

Buat file `admin.blade.php` pada path berikut:

```swift
resources/views/components/layouts/admin.blade.php
```

![image.png](attachment:d83f2c87-bea8-4b89-83df-067eb58e894a:image.png)

Isi dengan kode berikut:

```php
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <div class="drawer lg:drawer-open w-full min-h-screen bg-gray-50">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content">
            <!-- Navbar -->
            <nav class="navbar w-full bg-base-300">
                <label for="my-drawer-4" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                    <!-- Sidebar toggle icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor" class="my-1.5 inline-block size-4">
                        <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                        <path d="M9 4v16"></path>
                        <path d="M14 10l2 2l-2 2"></path>
                    </svg>
                </label>
            </nav>
            <!-- Page content -->
            {{ $slot }}
        </div>

        @include('components.admin.sidebar')
    </div>

    <footer class="bg-light text-center py-3">
        <div class="container">
            <p>© {{ date('Y') }} MyLaravelApp. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Section untuk script tambahan --}}
    @stack('scripts')
</body>

</html>
```

Pada bagian berikut, aplikasi menggunakan **Tailwind CSS** dan **DaisyUI** melalui CDN:

```php
<linkhref="https://cdn.jsdelivr.net/npm/daisyui@5"rel="stylesheet" />
<scriptsrc="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
```

- **Tailwind CSS** → Utility-first CSS framework untuk membangun UI secara cepat dan konsisten
- **DaisyUI** → Library komponen siap pakai (navbar, sidebar, button, card, alert, dll)

```php
<div>
	{{ --- Code Lainnya --- }}
	{{ $slot }}
</div>
```

`$slot` merupakan **Blade Component Slot**, yaitu area tempat konten halaman admin akan ditampilkan. Semua halaman yang menggunakan layout admin akan dirender di bagian ini.

```swift
<div>
	{{ --- Code Lainnya --- }}
	@include('components.admin.sidebar')
</div>
```

Kode di atas digunakan untuk **memanggil dan menampilkan komponen sidebar** ke dalam layout admin.

## Membuat Sidebar Admin

Buat file berikut:

```swift
resources/views/components/admin/sidebar.blade.php
```

Isi dengan kode berikut:

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
            <li class="">
                <a href="#" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Dashboard">
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
            <li class="">
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
            <button type="submit" class="btn btn-outline btn-error w-full is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Logout">
                    <!-- Logout icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M10 17v-2h4v-2h-4v-2l-5 3l5 3m9-12H5q-.825 0-1.413.588T3 7v10q0 .825.587 1.413T5 19h14q.825 0 1.413-.587T21 17v-3h-2v3H5V7h14v3h2V7q0-.825-.587-1.413T19 5z" />
                    </svg>
                    <span class="is-drawer-close:hidden">Logout</span>
                </button>
        </div>
    </div>
</div>
```