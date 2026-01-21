# **Module 10: Manajemen Tiket**

Modul ini bertujuan untuk membangun **fitur manajemen tiket** yang hanya dapat diakses oleh **admin** Admin dapat menambahkan, mengubah, dan menghapus tiket untuk setiap event.

---

## **Membuat Controller**

```bash
php artisan make:controller Admin/TiketController --resource
```

Edit file `app\Http\Controllers\Admin\TiketController.php` 

Controller ini bertanggung jawab untuk mengelola **CRUD (Create, Read, Update, Delete)** data Tiket yang diakses oleh admin.

1. Function `store`
    
    ```
    public function store(Request $request)
        {
            $validatedData = request()->validate([
                'event_id' => 'required|exists:events,id',
                'tipe' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'stok' => 'required|integer|min:0',
            ]);
    
            // Create the ticket
            Tiket::create($validatedData);
    
            return redirect()->route('admin.events.show', $validatedData['event_id'])->with('success', 'Ticket berhasil ditambahkan.');
        }
    ```
    
    ### Penjelasan
    
    - Melakukan validasi input tiket
    - Memastikan `event_id` benar-benar terdaftar di tabel `events`
    - Menyimpan data tiket ke database
    - Setelah berhasil, admin diarahkan kembali ke halaman **detail event**
    - Menampilkan notifikasi sukses
2. Function `update`
    
    ```
    public function update(Request $request, string $id)
        {
            $ticket = Tiket::findOrFail($id);
    
            $validatedData = $request->validate([
                'tipe' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'stok' => 'required|integer|min:0',
            ]);
    
            $ticket->update($validatedData);
    
            return redirect()->route('admin.events.show', $ticket->event_id)->with('success', 'Ticket berhasil diperbarui.');
        }
    ```
    
    ### Penjelasan
    
    - Mengambil data tiket berdasarkan ID
    - Memvalidasi data baru
    - Memperbarui data tiket di database
    - Mengarahkan kembali ke halaman detail event
    - Menampilkan notifikasi sukses
3. Function `destroy`
    
    ```
    public function destroy(string $id)
        {
            $ticket = Tiket::findOrFail($id);
            $eventId = $ticket->event_id;
            $ticket->delete();
    
            return redirect()->route('admin.events.show', $eventId)->with('success', 'Ticket berhasil dihapus.');
        }
    ```
    
    ### Penjelasan
    
    - Mengambil data tiket berdasarkan ID
    - Menyimpan `event_id` sebelum data dihapus
    - Menghapus tiket dari database
    - Kembali ke halaman detail event dengan notifikasi sukses

## Membuat View

Pada modul ini, tidak membuat view baru, tetapi hanya menambahkan fitur tiket pada halaman detail event pada file `resources/views/admin/event/show.blade.php`

1. Menampilkan tiket
tambahkan code ini di bagian bawah script untuk menampilkan detail event

    
    ```
    <div class="mt-10">
                <div class="flex">
                    <h1 class="text-3xl font-semibold mb-4">List Ticket</h1>
                    <button onclick="add_ticket_modal.showModal()" class="btn btn-primary ml-auto">Tambah Ticket</button>
                </div>
                <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th class="w-1/3">tipe</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $index => $ticket)
                                <tr>
                                    <th>{{ $index + 1 }}</th>
                                    <td>{{ $ticket->tipe }}</td>
                                    <td>{{ $ticket->harga }}</td>
                                    <td>{{ $ticket->stok }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary mr-2" onclick="openEditModal(this)"
                                            data-id="{{ $ticket->id }}" data-tipe="{{ $ticket->tipe }}"
                                            data-harga="{{ $ticket->harga }}"
                                            data-stok="{{ $ticket->stok }}">Edit</button>
                                        <button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)"
                                            data-id="{{ $ticket->id }}">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada ticket tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    ```
    
    Kode ini digunakan untuk menampilkan seluruh tiket yang terkait dengan event.
    
    **Fungsi utama:**
    
    - Menampilkan daftar tiket dalam bentuk tabel
    - Menyediakan tombol **Tambah, Edit, dan Hapus Ticket**
    - Menampilkan pesan jika belum ada tiket
2. Modal Tambah Tiket
    
    Tambahkan code ini
    
    ```php
    <!-- Add Ticket Modal -->
        <dialog id="add_ticket_modal" class="modal">
            <form method="POST" action="{{ route('admin.tickets.store') }}" class="modal-box">
                @csrf
    
                <h3 class="text-lg font-bold mb-4">Tambah Ticket</h3>
    
                <input type="hidden" name="event_id" value="{{ $event->id }}">
    
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Tipe Ticket</span>
                    </label>
                    <select name="tipe" class="select select-bordered w-full" required>
                        <option value="" disabled selected>Pilih Tipe Ticket</option>
                        <option value="reguler">Regular</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Harga</span>
                    </label>
                    <input type="number" name="harga" placeholder="Contoh: 50000" class="input input-bordered w-full"
                        required />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Stok</span>
                    </label>
                    <input type="number" name="stok" placeholder="Contoh: 100" class="input input-bordered w-full"
                        required />
                </div>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Tambah</button>
                    <button class="btn" onclick="add_ticket_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    ```
    
    Modal ini digunakan untuk **menambahkan tiket baru** ke event.
    
    **Penjelasan:**
    
    - Mengirim data ke route `admin.tickets.store`
    - `event_id` dikirim secara tersembunyi
    - Admin memilih tipe tiket, harga, dan stok
    - Validasi dilakukan di controller
    
    Form Edit
    Tambahkan code ini
    
    ```
    <!-- Edit Ticket Modal -->
        <dialog id="edit_ticket_modal" class="modal">
            <form method="POST" class="modal-box">
                @csrf
                @method('PUT')
    
                <input type="hidden" name="ticket_id" id="edit_ticket_id">
    
                <h3 class="text-lg font-bold mb-4">Edit Ticket</h3>
    
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Tipe Ticket</span>
                    </label>
                    <select name="tipe" id="edit_tipe" class="select select-bordered w-full" required>
                        <option value="" disabled selected>Pilih Tipe Ticket</option>
                        <option value="reguler">Regular</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Harga</span>
                    </label>
                    <input type="number" name="harga" id="edit_harga" placeholder="Contoh: 50000"
                        class="input input-bordered w-full" required />
                </div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Stok</span>
                    </label>
                    <input type="number" name="stok" id="edit_stok" placeholder="Contoh: 100"
                        class="input input-bordered w-full" required />
                </div>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <button class="btn" onclick="edit_ticket_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    ```
    
    Modal ini digunakan untuk **mengedit tiket yang sudah ada**.
    
    **Penjelasan:**
    
    - Data tiket diambil dari atribut `data-*` pada tombol
    - Form menggunakan method `PUT`
    - Action form diatur secara dinamis menggunakan JavaScript
3. Modal Delete
Tambahkan ini
    
    ```
    <!-- Delete Ticket Modal -->
        <dialog id="delete_modal" class="modal">
            <form method="POST" class="modal-box">
                @csrf
                @method('DELETE')
    
                <input type="hidden" name="ticket_id" id="delete_ticket_id">
    
                <h3 class="text-lg font-bold mb-4">Hapus Ticket</h3>
                <p>Apakah Anda yakin ingin menghapus ticket ini?</p>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Hapus</button>
                    <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    ```
    
    Modal ini digunakan untuk **konfirmasi penghapusan tiket**.
    
    **Penjelasan:**
    
    - Menggunakan method `DELETE`
    - Menampilkan pesan konfirmasi sebelum data dihapus
    - Endpoint ditentukan secara dinamis
4. Script Pendukung (JavaScript)
Tambahkan code ini
    
    ```
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
    
            function openDeleteModal(button) {
                const id = button.dataset.id;
                const form = document.querySelector('#delete_modal form');
                document.getElementById("delete_ticket_id").value = id;
    
                // Set action dengan parameter ID
                form.action = `/admin/tickets/${id}`;
                delete_modal.showModal();
            }
    
            function openEditModal(button) {
                const id = button.dataset.id;
                const tipe = button.dataset.tipe;
                const harga = button.dataset.harga;
                const stok = button.dataset.stok;
    
                const form = document.querySelector('#edit_ticket_modal form');
                document.getElementById("edit_ticket_id").value = id;
                document.getElementById("edit_tipe").value = tipe;
                document.getElementById("edit_harga").value = harga;
                document.getElementById("edit_stok").value = stok;
    
                // Set action dengan parameter ID
                form.action = `/admin/tickets/${id}`;
                edit_ticket_modal.showModal();
            }
        </script>
    ```
    
    Script ini berfungsi sebagai **penghubung antara UI dan backend**.
    
    ### Penjelasan fungsi script:
    
    - Mengambil data tiket dari tombol (`data-id`, `data-tipe`, `data-harga`, `data-stok`)
    - Mengisi field pada modal edit dan delete
    - Menentukan endpoint update dan delete secara dinamis
    - Menampilkan modal sesuai aksi yang dipilih admin
    - Menangani preview gambar event (fitur pendukung halaman detail event)

Sehingga isi dari `show.blade.php` :

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

        <div class="mt-10">
            <div class="flex">
                <h1 class="text-3xl font-semibold mb-4">List Ticket</h1>
                <button onclick="add_ticket_modal.showModal()" class="btn btn-primary ml-auto">Tambah Ticket</button>
            </div>
            <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th class="w-1/3">tipe</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $index => $ticket)
                            <tr>
                                <th>{{ $index + 1 }}</th>
                                <td>{{ $ticket->tipe }}</td>
                                <td>{{ $ticket->harga }}</td>
                                <td>{{ $ticket->stok }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary mr-2" onclick="openEditModal(this)"
                                        data-id="{{ $ticket->id }}" data-tipe="{{ $ticket->tipe }}"
                                        data-harga="{{ $ticket->harga }}"
                                        data-stok="{{ $ticket->stok }}">Edit</button>
                                    <button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)"
                                        data-id="{{ $ticket->id }}">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada ticket tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Ticket Modal -->
    <dialog id="add_ticket_modal" class="modal">
        <form method="POST" action="{{ route('admin.tickets.store') }}" class="modal-box">
            @csrf

            <h3 class="text-lg font-bold mb-4">Tambah Ticket</h3>

            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-semibold">Tipe Ticket</span>
                </label>
                <select name="tipe" class="select select-bordered w-full" required>
                    <option value="" disabled selected>Pilih Tipe Ticket</option>
                    <option value="reguler">Regular</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-semibold">Harga</span>
                </label>
                <input type="number" name="harga" placeholder="Contoh: 50000" class="input input-bordered w-full"
                    required />
            </div>
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-semibold">Stok</span>
                </label>
                <input type="number" name="stok" placeholder="Contoh: 100" class="input input-bordered w-full"
                    required />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Tambah</button>
                <button class="btn" onclick="add_ticket_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Edit Ticket Modal -->
    <dialog id="edit_ticket_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('PUT')

            <input type="hidden" name="ticket_id" id="edit_ticket_id">

            <h3 class="text-lg font-bold mb-4">Edit Ticket</h3>

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-semibold">Tipe Ticket</span>
                </label>
                <select name="tipe" id="edit_tipe" class="select select-bordered w-full" required>
                    <option value="" disabled selected>Pilih Tipe Ticket</option>
                    <option value="reguler">Regular</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-semibold">Harga</span>
                </label>
                <input type="number" name="harga" id="edit_harga" placeholder="Contoh: 50000"
                    class="input input-bordered w-full" required />
            </div>
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-semibold">Stok</span>
                </label>
                <input type="number" name="stok" id="edit_stok" placeholder="Contoh: 100"
                    class="input input-bordered w-full" required />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="edit_ticket_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Ticket Modal -->
    <dialog id="delete_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('DELETE')

            <input type="hidden" name="ticket_id" id="delete_ticket_id">

            <h3 class="text-lg font-bold mb-4">Hapus Ticket</h3>
            <p>Apakah Anda yakin ingin menghapus ticket ini?</p>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Hapus</button>
                <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

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

        function openDeleteModal(button) {
            const id = button.dataset.id;
            const form = document.querySelector('#delete_modal form');
            document.getElementById("delete_ticket_id").value = id;

            // Set action dengan parameter ID
            form.action = `/admin/tickets/${id}`;
            delete_modal.showModal();
        }

        function openEditModal(button) {
            const id = button.dataset.id;
            const tipe = button.dataset.tipe;
            const harga = button.dataset.harga;
            const stok = button.dataset.stok;

            const form = document.querySelector('#edit_ticket_modal form');
            document.getElementById("edit_ticket_id").value = id;
            document.getElementById("edit_tipe").value = tipe;
            document.getElementById("edit_harga").value = harga;
            document.getElementById("edit_stok").value = stok;

            // Set action dengan parameter ID
            form.action = `/admin/tickets/${id}`;
            edit_ticket_modal.showModal();
        }
    </script>
</x-layouts.admin>

```

## **Setup Routes**

Edit file `routes/web.php`, lalu tambahkan route khusus admin untuk Tiket.

Route ini **harus berada di dalam group admin middleware**, agar hanya admin yang bisa mengaksesnya.

```php
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Event Management
        Route::resource('events', EventController::class);

        // Tiket Management 
        Route::resource('tickets', TiketController::class);
    });
```

## Testing Fitur Manajemen Tiket

Lakukan pengujian dengan langkah berikut:

1. Login sebagai **Admin**
2. Akses halaman detail pada salah satu item Event
3. Uji fitur:
    - Tambah tiket
    - Edit tiket
    - Hapus tiket
4. Pastikan:
    - Data berubah di database
    - Notifikasi tampil dengan benar
    - User non-admin tidak bisa mengakses halaman ini