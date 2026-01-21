# **Module 9: Manajemen Event**

Modul ini bertujuan untuk membangun **fitur manajemen event dan tiket** yang hanya dapat diakses oleh **admin**.

Admin dapat:

- Mengelola data event (CRUD)
- Mengunggah gambar event

---

## **Membuat Controller**

```bash
php artisan make:controller Admin/EventController --resource
```

Edit file `app\Http\Controllers\Admin\EventController.php` 

Controller ini bertanggung jawab untuk mengelola **CRUD (Create, Read, Update, Delete)** data Event yang diakses oleh admin.

1. Function `index`
    
    ```
    public function index()
    {
    	$events = Event::all();
    	return view('admin.event.index', compact('events'));
    }
    ```
    
    ### Penjelasan
    
    Function `index` digunakan untuk **menampilkan daftar seluruh event** yang ada di database.
    
    Alur kerja:
    
    1. `Event::all()`
        - Mengambil seluruh data event dari tabel `events`.
    2. Data event disimpan dalam variabel `$events`.
    3. `compact('events')`
        - Mengirim data ke view `admin.event.index`.
    4. View akan menampilkan data event dalam bentuk tabel/list.
2. Function `create`
    
    ```
    public function create()
        {
            $categories = Kategori::all();
            return view('admin.event.create', compact('categories'));
        }
    ```
    
    ### Penjelasan
    
    Function `create` digunakan untuk **menampilkan form tambah event baru**.
    
    Alur kerja:
    
    1. `Kategori::all()`
        - Mengambil seluruh data kategori dari database.
    2. Data kategori dikirim ke view `admin.event.create`.
    3. Data kategori digunakan dropdown pilihan kategori event.
3. Function `store` 
    
    ```
    public function store(Request $request)
        {
            $validatedData = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'tanggal_waktu' => 'required|date',
                'lokasi' => 'required|string|max:255',
                'kategori_id' => 'required|exists:kategoris,id',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            // Handle file upload
            if ($request->hasFile('gambar')) {
                $imageName = time().'.'.$request->gambar->extension();
                $request->gambar->move(public_path('images/events'), $imageName);
                $validatedData['gambar'] = $imageName;
            }
    
            $validatedData['user_id'] = auth()->user()->id ?? null;
    
            Event::create($validatedData);
    
            return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan.');
        }
    ```
    
    ### Penjelasan
    
    Function `store` bertugas untuk **menyimpan event baru ke database**.
    
    1. Validasi Data
        - Memastikan semua field wajib diisi
        - Memastikan kategori valid (`exists:kategoris,id`)
        - Memastikan file adalah gambar dan ukurannya aman
    2.  Upload Gambar
        - Mengecek apakah file gambar dikirim
        - Memberi nama unik (berdasarkan timestamp)
        - Menyimpan gambar ke folder `public/images/events`
        - Nama file disimpan ke database
    3. Simpan Data Event
        - Menambahkan `user_id` (admin yang membuat event)
        - `Event::create()` menyimpan data ke database
4. Function `show`
    
    ```
    public function show(string $id)
        {
            $event = Event::findOrFail($id);
            $categories = Kategori::all();
            $tickets = $event->tikets;
    
            return view('admin.event.show', compact('event', 'categories', 'tickets'));
        }
    ```
    
    ### Penjelasan
    
    Function `show` digunakan untuk **menampilkan detail event beserta tiketnya**.
    
    Alur kerja:
    
    1. `Event::findOrFail($id)`
        - Mengambil event berdasarkan ID
        - Jika tidak ditemukan, otomatis error 404
    2. Mengambil seluruh kategori
    3. `$event->tikets`
        - Mengambil tiket yang terkait dengan event (relasi)
    4. Semua data dikirim ke view `admin.event.show`
5. Function `edit` 
    
    ```php
    public function edit(string $id)
        {
            $event = Event::findOrFail($id);
            $categories = Kategori::all();
            return view('admin.event.edit', compact('event', 'categories'));
        }
    ```
    
    ### Penjelasan
    
    Function `edit` menampilkan **form edit event** dengan data lama.
    
    Alur kerja:
    
    1. Mengambil data event berdasarkan ID
    2. Mengambil seluruh kategori
    3. Data dikirim ke view edit
    4. Form otomatis terisi dengan data event lama
6. Function `update`
    
    ```php
    public function update(Request $request, string $id)
        {
            try {
                $event = Event::findOrFail($id);
    
                $validatedData = $request->validate([
                    'judul' => 'required|string|max:255',
                    'deskripsi' => 'required|string',
                    'tanggal_waktu' => 'required|date',
                    'lokasi' => 'required|string|max:255',
                    'kategori_id' => 'required|exists:kategoris,id',
                    'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
    
                // Handle file upload
                if ($request->hasFile('gambar')) {
                    $imageName = time().'.'.$request->gambar->extension();
                    $request->gambar->move(public_path('images/events'), $imageName);
                    $validatedData['gambar'] = $imageName;
                }
    
                $event->update($validatedData);
    
                return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui event: ' . $e->getMessage()]);
            }
        }
    ```
    
    ### Penjelasan
    
    Function `update` digunakan untuk **memperbarui data event yang sudah ada**.
    
    Perbedaan utama dengan `store`:
    
    - Gambar **tidak wajib** (`nullable`)
    - Data lama akan ditimpa dengan data baru
    
    Menggunakan `try-catch` untuk:
    
    - Menangani error database
    - Memberikan pesan error yang jelas
7. Function `destroy`
    
    ```
    public function destroy(string $id)
        {
            $event = Event::findOrFail($id);
            $event->delete();
    
            return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
        }
    ```
    
    ### Penjelasan
    
    Function `destroy` digunakan untuk **menghapus event**.
    

## Storage Link

digunakan untuk membuat **symbolic link (shortcut)** antara folder **`storage/app/public`** dengan **`public/storage`**.

Secara default, Laravel menyimpan file upload (gambar, dokumen, dll) di dalam folder `storage`, yang **tidak bisa diakses langsung oleh browser**. Dengan storage link, file-file tersebut dapat diakses secara publik melalui URL.

Jalankan perintah berikut di terminal:

```php
php artisan storage:link
```

## Membuat View

Pada tahap ini, dibuat beberapa file **Blade View** yang berfungsi sebagai antarmuka (UI) bagi admin untuk mengelola data **Event**. View ini akan terhubung langsung dengan **EventController** dan menampilkan data yang dikirim dari controller.

![image.png](attachment:6b6adffb-38f3-4ea0-858c-1892c3508d23:image.png)

1.  `create.blade.php`
**Lokasi:**
`resources/views/admin/event/create.blade.php`
    
    ```php
    <x-layouts.admin title="Tambah Event Baru">
        @if ($errors->any())
            <div class="toast toast-bottom toast-center z-50">
                <ul class="alert alert-error">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
    
            <script>
                setTimeout(() => {
                    document.querySelector('.toast')?.remove()
                }, 5000)
            </script>
        @endif
    
        <div class="container mx-auto p-10">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">Tambah Event Baru</h2>
    
                    <form id="eventForm" class="space-y-4" method="post" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Nama Event -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Judul Event</span>
                            </label>
                            <input
                                type="text"
                                name="judul"
                                placeholder="Contoh: Konser Musik Rock"
                                class="input input-bordered w-full"
                                required />
                        </div>
    
                        <!-- Deskripsi -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Deskripsi</span>
                            </label>
                            <br>
                            <textarea
                                name="deskripsi"
                                placeholder="Deskripsi lengkap tentang event..."
                                class="textarea textarea-bordered h-24 w-full"
                                required></textarea>
                        </div>
    
                        <!-- Tanggal & Waktu -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Tanggal & Waktu</span>
                            </label>
                            <input
                                type="datetime-local"
                                name="tanggal_waktu"
                                class="input input-bordered w-full"
                                required />
                        </div>
    
                        <!-- Lokasi -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Lokasi</span>
                            </label>
                            <input
                                type="text"
                                name="lokasi"
                                placeholder="Contoh: Stadion Utama"
                                class="input input-bordered w-full"
                                required />
                        </div>
    
                        <!-- Kategori -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Kategori</span>
                            </label>
                            <select name="kategori_id" class="select select-bordered w-full" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nama }}</option>
                                @endforeach
                            </select>
                        </div>
    
                        <!-- Upload Gambar -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Gambar Event</span>
                            </label>
                            <input
                                type="file"
                                name="gambar"
                                accept="image/*"
                                class="file-input file-input-bordered w-full"
                                required />
                            <label class="label">
                                <span class="label-text-alt">Format: JPG, PNG, max 5MB</span>
                            </label>
                        </div>
    
                        <!-- Preview Gambar -->
                        <div id="imagePreview" class="hidden overflow-hidden">
                            <label class="label">
                                <span class="label-text font-semibold">Preview Gambar</span>
                            </label>
                            <br>
                            <div class="avatar max-w-sm">
                                <div class="w-full rounded-lg">
                                    <img id="previewImg" src="" alt="Preview">
                                </div>
                            </div>
                        </div>
    
                        <!-- Tombol Submit -->
                        <div class="card-actions justify-end mt-6">
                            <button type="reset" class="btn btn-ghost">Reset</button>
                            <button type="submit" class="btn btn-primary">Simpan Event</button>
                        </div>
                    </form>
                </div>
            </div>
    
            <!-- Alert Success -->
            <div id="successAlert" class="alert alert-success mt-4 hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Event berhasil disimpan!</span>
            </div>
        </div>
    
        <script>
            const form = document.getElementById('eventForm');
            const fileInput = form.querySelector('input[type="file"]');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const successAlert = document.getElementById('successAlert');
    
            // Preview gambar saat dipilih
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
    
            // Handle reset
            form.addEventListener('reset', function() {
                imagePreview.classList.add('hidden');
                successAlert.classList.add('hidden');
            });
        </script>
    </x-layouts.admin>
    ```
    
    **Penjelasan:**
    
    - Menyediakan input seperti judul, deskripsi, tanggal & waktu, lokasi, kategori, dan gambar event
    - Data kategori diambil dari controller (`$categories`)
    - Form dikirim ke route `admin.events.store`
    - Digunakan oleh function `create()` dan `store()` pada controller
2. `edit.blade.php`
    
    **Lokasi:**
    
    `resources/views/admin/event/edit.blade.php`
    
    ```php
    <x-layouts.admin title="Edit Event">
        <div class="container mx-auto p-10">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">Edit Event</h2>
    
                    <form id="eventForm" class="space-y-4" method="post"
                        action="{{ route('admin.events.update', $event->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Nama Event -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Judul Event</span>
                            </label>
                            <input type="text" name="judul" placeholder="Contoh: Konser Musik Rock"
                                class="input input-bordered w-full" value="{{ $event->judul }}" required />
                        </div>
    
                        <!-- Deskripsi -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Deskripsi</span>
                            </label>
                            <br>
                            <textarea name="deskripsi" placeholder="Deskripsi lengkap tentang event..."
                                class="textarea textarea-bordered h-24 w-full" required>{{ $event->deskripsi }}</textarea>
                        </div>
    
                        <!-- Tanggal & Waktu -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Tanggal & Waktu</span>
                            </label>
                            <input type="datetime-local" name="tanggal_waktu" class="input input-bordered w-full"
                                value="{{ $event->tanggal_waktu->format('Y-m-d\TH:i') }}" required />
                        </div>
    
                        <!-- Lokasi -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Lokasi</span>
                            </label>
                            <input type="text" name="lokasi" placeholder="Contoh: Stadion Utama"
                                class="input input-bordered w-full" value="{{ $event->lokasi }}" required />
                        </div>
    
                        <!-- Kategori -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Kategori</span>
                            </label>
                            <select name="kategori_id" class="select select-bordered w-full" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $category->id == $event->kategori_id ? 'selected' : '' }}>
                                        {{ $category->nama }}
                                    </option>
                                @endforeach
    
                            </select>
                        </div>
    
                        <!-- Upload Gambar -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Gambar Event</span>
                            </label>
                            <input type="file" name="gambar" accept="image/*"
                                class="file-input file-input-bordered w-full" />
                            <label class="label">
                                <span class="label-text-alt">Format: JPG, PNG, max 5MB</span>
                            </label>
                        </div>
    
                        <!-- Preview Gambar -->
                        <div id="imagePreview" class="overflow-hidden {{ $event->gambar ? '' : 'hidden' }}">
                            <label class="label">
                                <span class="label-text font-semibold">Preview Gambar</span>
                            </label>
                            <br>
                            <div class="avatar max-w-sm">
                                <div class="w-full rounded-lg">
                                    @if ($event->gambar)
                                        <img id="previewImg" src="{{ asset('images/events/' . $event->gambar) }}"
                                            alt="Preview">
                                    @else
                                        <img id="previewImg" src="" alt="Preview">
                                    @endif
                                </div>
                            </div>
                        </div>
    
                        <!-- Tombol Submit -->
                        <div class="card-actions justify-end mt-6">
                            <button type="reset" class="btn btn-ghost">Reset</button>
                            <button type="submit" class="btn btn-primary">Simpan Event</button>
                        </div>
                    </form>
                </div>
            </div>
    
            <!-- Alert Success -->
            <div id="successAlert" class="alert alert-success mt-4 hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Event berhasil disimpan!</span>
            </div>
        </div>
    
        <script>
            const form = document.getElementById('eventForm');
            const fileInput = form.querySelector('input[type="file"]');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const successAlert = document.getElementById('successAlert');
    
            // Preview gambar saat dipilih
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
    
            // Handle reset
            form.addEventListener('reset', function() {
                imagePreview.classList.add('hidden');
                successAlert.classList.add('hidden');
            });
        </script>
    </x-layouts.admin>
    
    ```
    
    **Penjelasan:**
    
    - Field form terisi otomatis berdasarkan data event
    - Admin dapat memperbarui informasi event dan mengganti gambar
    - Form dikirim ke route `admin.events.update`
    - Digunakan oleh function `edit()` dan `update()` pada controller
3. `index.blade.php` 
**Lokasi:**
`resources/views/admin/event/index.blade.php`
    
    ```php
    <x-layouts.admin title="Manajemen Event">
        @if (session('success'))
            <div class="toast toast-bottom toast-center">
                <div class="alert alert-success">
                    <span>{{ session('success') }}</span>
                </div>
            </div>
    
            <script>
            setTimeout(() => {
                document.querySelector('.toast')?.remove()
            }, 3000)
            </script>
        @endif
    
        <div class="container mx-auto p-10">
            <div class="flex">
                <h1 class="text-3xl font-semibold mb-4">Manajemen Event</h1>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary ml-auto">Tambah Event</a>
            </div>
            <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th class="w-1/3">Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $index => $event)
                        <tr>
                            <th>{{ $index + 1 }}</th>
                            <td>{{ $event->judul }}</td>
                            <td>{{ $event->kategori->nama }}</td>
                            <td>{{ $event->tanggal_waktu->format('d M Y') }}</td>
                            <td>{{ $event->lokasi }}</td>
                            <td>
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-sm btn-info mr-2">Detail</a>
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-primary mr-2">Edit</a>
                                <button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)" data-id="{{ $event->id }}">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada event tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    
        <!-- Delete Modal -->
        <dialog id="delete_modal" class="modal">
            <form method="POST" class="modal-box">
                @csrf
                @method('DELETE')
    
                <input type="hidden" name="event_id" id="delete_event_id">
    
                <h3 class="text-lg font-bold mb-4">Hapus Event</h3>
                <p>Apakah Anda yakin ingin menghapus event ini?</p>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Hapus</button>
                    <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    
        <script>
            function openDeleteModal(button) {
                const id = button.dataset.id;
                const form = document.querySelector('#delete_modal form');
                document.getElementById("delete_event_id").value = id;
    
                // Set action dengan parameter ID
                form.action = `/admin/events/${id}`
    
                delete_modal.showModal();
            }
    </script>
    
    </x-layouts.admin>
    ```
    
    **Penjelasan:**
    
    - Menampilkan data event (judul, kategori, tanggal, lokasi, dll)
    - Tersedia tombol **Detail, Edit, dan Hapus**
    - Data dikirim dari function `index()` pada controller
    - Menjadi halaman utama manajemen event untuk admin
4. `show.blade.php`
**Lokasi:**
`resources/views/admin/event/show.blade.php`
    
    ```php
    <x-layouts.admin title="Detail Event">
        <div class="container mx-auto p-10">
            @if (session('success'))
                <div class="toast toast-bottom toast-center z-50">
                    <div class="alert alert-success">
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
    
                <script>
                    setTimeout(() => {
                        document.querySelector('.toast')?.remove()
                    }, 3000)
                </script>
            @endif
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-6">Detail Event</h2>
    
                    <form id="eventForm" class="space-y-4" method="post"
                        action="{{ route('admin.events.update', $event->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Nama Event -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Judul Event</span>
                            </label>
                            <input type="text" name="judul" placeholder="Contoh: Konser Musik Rock"
                                class="input input-bordered w-full" value="{{ $event->judul }}" disabled required />
                        </div>
    
                        <!-- Deskripsi -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Deskripsi</span>
                            </label>
                            <br>
                            <textarea name="deskripsi" placeholder="Deskripsi lengkap tentang event..."
                                class="textarea textarea-bordered h-24 w-full" disabled required>{{ $event->deskripsi }}</textarea>
                        </div>
    
                        <!-- Tanggal & Waktu -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Tanggal & Waktu</span>
                            </label>
                            <input type="datetime-local" name="tanggal_waktu" class="input input-bordered w-full"
                                value="{{ $event->tanggal_waktu->format('Y-m-d\TH:i') }}" disabled required />
                        </div>
    
                        <!-- Lokasi -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Lokasi</span>
                            </label>
                            <input type="text" name="lokasi" placeholder="Contoh: Stadion Utama"
                                class="input input-bordered w-full" value="{{ $event->lokasi }}" disabled required />
                        </div>
    
                        <!-- Kategori -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Kategori</span>
                            </label>
                            <select name="kategori_id" class="select select-bordered w-full" required disabled>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $category->id == $event->kategori_id ? 'selected' : '' }}>
                                        {{ $category->nama }}
                                    </option>
                                @endforeach
    
                            </select>
                        </div>
    
                        <!-- Upload Gambar -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Gambar Event</span>
                            </label>
                            <input type="file" name="gambar" accept="image/*"
                                class="file-input file-input-bordered w-full" disabled />
                            <label class="label">
                                <span class="label-text-alt">Format: JPG, PNG, max 5MB</span>
                            </label>
                        </div>
    
                        <!-- Preview Gambar -->
                        <div id="imagePreview" class="overflow-hidden {{ $event->gambar ? '' : 'hidden' }}">
                            <label class="label">
                                <span class="label-text font-semibold">Preview Gambar</span>
                            </label>
                            <br>
                            <div class="avatar max-w-sm">
                                <div class="w-full rounded-lg">
                                    @if ($event->gambar)
                                        <img id="previewImg" src="{{ asset('images/events/' . $event->gambar) }}"
                                            alt="Preview">
                                    @else
                                        <img id="previewImg" src="" alt="Preview">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
        <script>
            const form = document.getElementById('eventForm');
            const fileInput = form.querySelector('input[type="file"]');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const successAlert = document.getElementById('successAlert');
    
            // Preview gambar saat dipilih
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
    
        </script>
    </x-layouts.admin>
    
    ```
    
    **Penjelasan:**
    
    - Menampilkan informasi detail event (deskripsi, gambar, kategori, tiket)
    - Digunakan untuk melihat data tanpa mengedit
    - Data dikirim dari function `show()` pada controller
    - Biasanya diakses dari tombol “Detail” di halaman index

## **Setup Routes**

Edit file `routes/web.php`, lalu tambahkan route khusus admin untuk event.

Route ini **harus berada di dalam group admin middleware**, agar hanya admin yang bisa mengaksesnya.

```php
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Event Management
        Route::resource('events', EventController::class);
    });
```

## Update Sidebar

Agar menu kategori mengarah ke Manajemen Kategori edit file: `resources/views/components/admin/sidebar.blade.php`

```
<!-- Event item -->
<li class="{{ request()->routeIs('admin.events.*') ? 'bg-gray-200 rounded-lg' : '' }}">
	<a href="{{ route('admin.events.index') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Event">
		<!-- icon Event -->
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
			<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V7a2 2 0 0 1 2-2" />
		</svg>
		<span class="is-drawer-close:hidden">Manajemen Event</span>
	</a>
</li>
```

## Testing Fitur Manajemen Event

Lakukan pengujian dengan langkah berikut:

1. Login sebagai **Admin**
2. Akses halaman dengan menekan menu Manajemen Event pada sidebar
3. Uji fitur:
    - Tambah event
    - Edit event
    - Hapus event
4. Pastikan:
    - Data berubah di database
    - Notifikasi tampil dengan benar
    - User non-admin tidak bisa mengakses halaman ini