# Module 8 : Manajemen **Kategori**

Modul ini akan membuat fitur manajemen kategori event untuk admin.

Fitur ini mencakup:

- Menampilkan daftar kategori
- Menambahkan kategori baru
- Mengedit kategori
- Menghapus kategori

Semua fitur akan dikelola melalui **Admin Dashboard**.

---

## **Membuat Controller**

```bash
php artisan make:controller Admin/CategoryController --resource
```

Perintah ini akan membuat file: `app/Http/Controllers/Admin/CategoryController.php`

![image.png](attachment:f03c0829-2b0b-4717-b4f9-8f035127c45d:image.png)

Controller resource otomatis menyediakan method:

- `index()` → menampilkan data
- `store()` → menyimpan data
- `update()` → mengubah data
- `destroy()` → menghapus data

Edit file: `app/Http/Controllers/Admin/CategoryController.php` 

Controller ini bertanggung jawab untuk mengelola **CRUD (Create, Read, Update, Delete)** data **Kategori** yang diakses oleh admin.

1. Function `index`
    
    ```php
    public function index()
    {
    		$categories = Kategori::all();
    		return view('admin.category.index', compact('categories'));
    }
    ```
    
    ### Penjelasan
    
    Function `index` berfungsi untuk **menampilkan daftar seluruh kategori** yang ada di database.
    
    Penjelasan baris per baris:
    
    - `Kategori::all()`
        
        Mengambil seluruh data kategori dari tabel `kategori`.
        
    - `$categories`
        
        Menyimpan data kategori dalam bentuk collection.
        
    - `view('pages.admin.category.index', compact('categories'))`
        
        Mengirim data `$categories` ke halaman view admin kategori agar dapat ditampilkan dalam tabel.
        
2. Function `store` 
    
    ```
    public function store(Request $request)
        {
            $payload = $request->validate([
                'nama' => 'required|string|max:255',
            ]);
    
            if (!isset($payload['nama'])) {
                return redirect()->route('categories.index')->with('error', 'Nama kategori wajib diisi.');
            }
    
            Kategori::create([
                'nama' => $payload['nama'],
            ]);
    
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
        }
    
    ```
    
    ### Penjelasan
    
    Function `store` digunakan untuk **menyimpan data kategori baru** ke database.
    
    Alur proses:
    
    1. `$request->validate()`
        
        Melakukan validasi input:
        
        - `nama` wajib diisi
        - Bertipe string
        - Maksimal 255 karakter
    2. Pengecekan tambahan:
        
        ```php
        if (!isset($payload['nama']))
        ```
        
        Digunakan sebagai **pengaman tambahan**, meskipun validasi sudah dilakukan.
        
    3. `Kategori::create()`
        
        Menyimpan data kategori baru ke database.
        
    4. `with('success', ...)`
        
        Mengirim pesan sukses ke session untuk ditampilkan di halaman view.
        
3. Function `update`
    
    ```php
    public function update(Request $request, string $id)
        {
            $payload = $request->validate([
                'nama' => 'required|string|max:255',
            ]);
    
            if (!isset($payload['nama'])) {
                return redirect()->route('categories.index')->with('error', 'Nama kategori wajib diisi.');
            }
    
            $category = Kategori::findOrFail($id);
            $category->nama = $payload['nama'];
            $category->save();
    
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
        }
    ```
    
    ### Penjelasan
    
    Function `update` digunakan untuk **memperbarui data kategori yang sudah ada**.
    
    Alur proses:
    
    1. Validasi input sama seperti function `store`
    2. `Kategori::findOrFail($id)`
        
        Mengambil data kategori berdasarkan ID.
        
        - Jika ID tidak ditemukan, Laravel otomatis menampilkan error **404**.
    3. `$category->nama = ...`
        
        Mengubah nilai nama kategori.
        
    4. `$category->save()`
        
        Menyimpan perubahan ke database.
        
    5. Redirect kembali ke halaman kategori dengan pesan sukses.
4. Function `delete`
    
    ```
    public function destroy(string $id)
        {
            Kategori::destroy($id);
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
        }
    ```
    
    ### Penjelasan
    
    Function `destroy` digunakan untuk **menghapus data kategori** berdasarkan ID.
    
    Penjelasan:
    
    - `Kategori::destroy($id)`
        
        Menghapus data kategori dengan ID tertentu dari database.
        
    - Redirect kembali ke halaman kategori
    - Menampilkan notifikasi sukses setelah penghapusan

## **Setup Routes**

Edit file `routes/web.php`, lalu tambahkan route khusus admin untuk kategori.

Route ini **harus berada di dalam group admin middleware**, agar hanya admin yang bisa mengaksesnya.

```php
use App\Http\Controllers\Admin\CategoryController;

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Category Management
        Route::resource('categories', CategoryController::class);
    });
```

Sehingga isi dari file `routes/web.php`

![image.png](attachment:ba0bf485-4278-4834-985d-9e56034691d5:image.png)

## Update Sidebar

Agar menu kategori mengarah ke Manajemen Kategori edit file: `resources/views/components/admin/sidebar.blade.php`

```
<!-- Kategori item -->
<li class="{{ request()->routeIs('admin.categories.*') ? 'bg-gray-200 rounded-lg' : '' }}">
	<a href="{{ route('admin.categories.index') }}" class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Kategori">
		<!-- icon Kategori -->
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
			<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 3a3 3 0 1 0 6 0a3 3 0 1 0-6 0" />
		</svg>
		<span class="is-drawer-close:hidden">Manajemen Kategori</span>
	</a>
</li>
```

## Membuat View Manajemen Kategori

Buat folder dan file berikut:

`resources/views/pages/admin/category/index.blade.php`

![image.png](attachment:79fc841d-23eb-491d-b44c-1e37f1586858:image.png)

Isi dengan code berikut : 

```php
<x-layouts.admin title="Manajemen Kategori">
   
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
            <h1 class="text-3xl font-semibold mb-4">Manajemen Kategori</h1>
            <button class="btn btn-primary ml-auto" onclick="add_modal.showModal()">Tambah Kategori</button>
        </div>
        <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
            <table class="table">
                <!-- head -->
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="w-3/4">Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                        <tr>
                            <th>{{ $index + 1 }}</th>
                            <td>{{ $category->nama }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary mr-2" onclick="openEditModal(this)" data-id="{{ $category->id }}" data-nama="{{ $category->nama }}">Edit</button>
                                <button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)" data-id="{{ $category->id }}">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada kategori tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category Modal -->
    <dialog id="add_modal" class="modal">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="modal-box">
            @csrf
            <h3 class="text-lg font-bold mb-4">Tambah Kategori</h3>
            <div class="form-control w-full mb-4">
                <label class="label mb-2">
                    <span class="label-text">Nama Kategori</span>
                </label>
                <input type="text" placeholder="Masukkan nama kategori" class="input input-bordered w-full" name="nama" required />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="add_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Edit Category Modal With Retrieve ID -->
     <dialog id="edit_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('PUT')

            <input type="hidden" name="category_id" id="edit_category_id">

            <h3 class="text-lg font-bold mb-4">Edit Kategori</h3>
            <div class="form-control w-full mb-4">
                <label class="label mb-2">
                    <span class="label-text">Nama Kategori</span>
                </label>
                <input type="text" placeholder="Masukkan nama kategori" class="input input-bordered w-full" value="Kategori Contoh" id="edit_category_name" name="nama" />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="edit_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Modal -->
    <dialog id="delete_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('DELETE')

            <input type="hidden" name="category_id" id="delete_category_id">

            <h3 class="text-lg font-bold mb-4">Hapus Kategori</h3>
            <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Hapus</button>
                <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        function openEditModal(button) {
            const name = button.dataset.nama;
            const id = button.dataset.id;
            const form = document.querySelector('#edit_modal form');
            
            document.getElementById("edit_category_name").value = name;
            document.getElementById("edit_category_id").value = id;

             // Set action dengan parameter ID
            form.action = `/admin/categories/${id}`

            edit_modal.showModal();
        }

        function openDeleteModal(button) {
            const id = button.dataset.id;
            const form = document.querySelector('#delete_modal form');
            document.getElementById("delete_category_id").value = id;

            // Set action dengan parameter ID
            form.action = `/admin/categories/${id}`

            delete_modal.showModal();
        }
</script>

</x-layouts.admin>
```

1. Notifikasi Success
    
    ```
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
    ```
    
    ### **Penjelasan**
    
    Kode ini digunakan untuk **menampilkan notifikasi sukses** berdasarkan data yang dikirim melalui session.
    
    Notifikasi akan muncul setelah:
    
    - Menambah data kategori
    - Mengedit data kategori
    - Menghapus data kategori
    
    JavaScript `setTimeout()` digunakan untuk:
    
    - Menghapus elemen notifikasi secara otomatis
    - Memberikan pengalaman pengguna yang lebih baik
    - Menghindari notifikasi menumpuk di layar
    
    Notifikasi akan **hilang otomatis setelah 3 detik**.
    
2. Tabel Kategori
    
    ```
    @forelse ($categories as $index => $category)
    <tr>
    	<th>{{ $index + 1 }}</th>
    	<td>{{ $category->nama }}</td>
    	<td>
    		<button class="btn btn-sm btn-primary mr-2" onclick="openEditModal(this)" data-id="{{ $category->id }}" data-nama="{{ $category->nama }}">Edit</button>
    		<button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)" data-id="{{ $category->id }}">Hapus</button>
    	</td>
    </tr>
    @empty
    <tr>
    	<td colspan="3" class="text-center">Tidak ada kategori tersedia.</td>
    </tr>
    @endforelse
    ```
    
    ### Penjelasan
    
    - `@forelse` digunakan untuk melakukan perulangan data kategori
    - Menampilkan:
        - Nomor urut
        - Nama kategori
        - Tombol aksi (Edit & Hapus)
    - Jika data kategori kosong, maka akan menampilkan pesan:
        
        > “Tidak ada kategori tersedia.”
        > 
    
    Atribut `data-id` dan `data-nama` digunakan untuk:
    
    - Mengirim data kategori ke JavaScript
    - Mengisi modal edit dan delete secara dinamis
3. Modal Tambah Kategori
    
    ```
    <!-- Add Category Modal -->
        <dialog id="add_modal" class="modal">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="modal-box">
                @csrf
                <h3 class="text-lg font-bold mb-4">Tambah Kategori</h3>
                <div class="form-control w-full mb-4">
                    <label class="label mb-2">
                        <span class="label-text">Nama Kategori</span>
                    </label>
                    <input type="text" placeholder="Masukkan nama kategori" class="input input-bordered w-full" name="nama" required />
                </div>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <button class="btn" onclick="add_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    ```
    
    ### Penjelasan
    
    - Digunakan untuk **menambahkan kategori baru**
    - Data dikirim ke route `categories.store`
    - Menggunakan method `POST`
    - Field `nama` bersifat **wajib diisi**
    - Menggunakan `@csrf` untuk keamanan form
4. Modal Edit Kategori
    
    ```
    <!-- Edit Category Modal With Retrieve ID -->
         <dialog id="edit_modal" class="modal">
            <form method="POST" class="modal-box">
                @csrf
                @method('PUT')
    
                <input type="hidden" name="category_id" id="edit_category_id">
    
                <h3 class="text-lg font-bold mb-4">Edit Kategori</h3>
                <div class="form-control w-full mb-4">
                    <label class="label mb-2">
                        <span class="label-text">Nama Kategori</span>
                    </label>
                    <input type="text" placeholder="Masukkan nama kategori" class="input input-bordered w-full" value="Kategori Contoh" id="edit_category_name" name="nama" />
                </div>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <button class="btn" onclick="edit_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    ```
    
    ### Penjelasan
    
    - Digunakan untuk **mengubah data kategori**
    - Menggunakan method `PUT`
    - Data kategori diisi secara otomatis melalui JavaScript
    - `@method('PUT')` digunakan untuk spoofing HTTP method
    - Action form diatur secara dinamis sesuai ID kategori
5. Modal Hapus Kategori
    
    ```
    <!-- Delete Modal -->
        <dialog id="delete_modal" class="modal">
            <form method="POST" class="modal-box">
                @csrf
                @method('DELETE')
    
                <input type="hidden" name="category_id" id="delete_category_id">
    
                <h3 class="text-lg font-bold mb-4">Hapus Kategori</h3>
                <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
                <div class="modal-action">
                    <button class="btn btn-primary" type="submit">Hapus</button>
                    <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
                </div>
            </form>
        </dialog>
    ```
    
    ### Penjelasan
    
    - Digunakan untuk **konfirmasi penghapusan data**
    - Menggunakan method `DELETE`
    - Mencegah penghapusan data secara tidak sengaja
    - ID kategori dikirim melalui input hidden
6. JavaScript Modal
    
    ```
    <script>
            function openEditModal(button) {
                const name = button.dataset.nama;
                const id = button.dataset.id;
                const form = document.querySelector('#edit_modal form');
                
                document.getElementById("edit_category_name").value = name;
                document.getElementById("edit_category_id").value = id;
    
                 // Set action dengan parameter ID
                form.action = `/admin/categories/${id}`
    
                edit_modal.showModal();
            }
    
            function openDeleteModal(button) {
                const id = button.dataset.id;
                const form = document.querySelector('#delete_modal form');
                document.getElementById("delete_category_id").value = id;
    
                // Set action dengan parameter ID
                form.action = `/admin/categories/${id}`
    
                delete_modal.showModal();
            }
    ```
    
    ### Penjelasan
    
    Script ini digunakan untuk:
    
    - Mengambil data kategori dari tombol (`data-id`, `data-nama`)
    - Mengisi field input pada modal edit & delete
    - Menentukan endpoint update dan delete secara dinamis
    - Menampilkan modal sesuai aksi yang dipilih

## Testing Fitur Manajemen Kategori

Lakukan pengujian dengan langkah berikut:

1. Login sebagai **Admin**
2. Akses halaman dengan menekan menu Manajemen Kategori pada sidebar
3. Uji fitur:
    - Tambah kategori
    - Edit kategori
    - Hapus kategori
4. Pastikan:
    - Data berubah di database
    - Notifikasi tampil dengan benar
    - User non-admin tidak bisa mengakses halaman ini