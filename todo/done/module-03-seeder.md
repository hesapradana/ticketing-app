# Module 3: Seeder

Dokumentasi lengkap semua Seeder yang digunakan untuk mengisi data awal aplikasi ticketing.

---

## Pengenalan Database Seeder

Setelah seluruh tabel pada database berhasil dibuat, langkah selanjutnya adalah **mengisi tabel dengan data awal (dummy/default)**.

Laravel menyediakan fitur **Database Seeder** yang berfungsi untuk mengisi data ke dalam database secara otomatis.

Penggunaan Seeder memiliki beberapa manfaat, antara lain:

- Menghindari input data secara manual
- Mempercepat proses pengembangan
- Menjaga konsistensi data awal
- Memudahkan proses testing dan development

## Lokasi File Seeder

File Seeder disimpan pada folder:

```
database/seeders
```

## Membuat dan Mengisi Seeder

Pada tahap ini, kita akan membuat dan mengisi file Seeder untuk masing-masing tabel yang diperlukan.

1. UserSeeder
Jalankan pada terminal
    
    ```php
    php artisan make:seeder UserSeeder
    ```
    
    File: `database/seeders/UserSeeder.php` 
    
    Seeder untuk membuat user admin dan user biasa.
    
    ```php
    <?php
    
    namespace Database\Seeders;
    
    use App\Models\User;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class UserSeeder extends Seeder
    {
        public function run(): void
        {
            $users = [
                [
                    'name' => 'Admin User',
                    'email' => 'admin@gmail.com',
                    'password' => bcrypt('password'),
                    'role' => 'admin',
                ],
                [
                    'name' => 'Regular User',
                    'email' => 'user@gmail.com',
                    'password' => bcrypt('password'),
                    'no_hp' => '081234567890',
                    'role' => 'user',
                ]
            ];
    
            foreach ($users as $user) {
                User::create($user);
            }
        }
    }
    ```
    
    **Data yang dibuat:**
    - Admin: `admin@gmail.com` / `password`
    - User: `user@gmail.com` / `password`
    

---

1. CategorySeeder
Jalankan pada terminal
    
    ```php
    php artisan make:seeder CategorySeeder
    ```
    
    File: `database/seeders/CategorySeeder.php` 
    Seeder untuk membuat kategori event.
    
    ```php
    <?php
    
    namespace Database\Seeders;
    
    use App\Models\Kategori;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class CategorySeeder extends Seeder
    {
        public function run(): void
        {
            $kategoris = [
                ['nama_kategori' => 'Konser'],
                ['nama_kategori' => 'Seminar'],
                ['nama_kategori' => 'Workshop'],
            ];
    
            foreach ($kategoris as $kategori) {
                Kategori::create(['nama' => $kategori['nama_kategori']]);
            }
        }
    }
    ```
    
    **Data yang dibuat:**
    - Konser
    - Seminar
    - Workshop
    

---

1. EventSeeder
Jalankan pada terminal
    
    ```php
    php artisan make:seeder EventSeeder
    ```
    
    File: `database/seeders/EventSeeder.php`  
    Seeder untuk membuat event contoh.
    
    ```php
    <?php
    
    namespace Database\Seeders;
    
    use App\Models\Event;
    use Illuminate\Database\Seeder;
    
    class EventSeeder extends Seeder
    {
        public function run(): void
        {
            $events = [
                [
                    'user_id' => 1,
                    'judul' => 'Konser Musik Rock',
                    'deskripsi' => 'Nikmati malam penuh energi dengan band rock terkenal.',
                    'tanggal_waktu' => '2024-08-15 19:00:00',
                    'lokasi' => 'Stadion Utama',
                    'kategori_id' => 1,
                    'gambar' => 'events/konser_rock.jpg',
                ],
                [
                    'user_id' => 1,
                    'judul' => 'Pameran Seni Kontemporer',
                    'deskripsi' => 'Jelajahi karya seni modern dari seniman lokal dan internasional.',
                    'tanggal_waktu' => '2024-09-10 10:00:00',
                    'lokasi' => 'Galeri Seni Kota',
                    'kategori_id' => 2,
                    'gambar' => 'events/pameran_seni.jpg',
                ],
                [
                    'user_id' => 1,
                    'judul' => 'Festival Makanan Internasional',
                    'deskripsi' => 'Cicipi berbagai hidangan lezat dari seluruh dunia.',
                    'tanggal_waktu' => '2024-10-05 12:00:00',
                    'lokasi' => 'Taman Kota',
                    'kategori_id' => 3,
                    'gambar' => 'events/festival_makanan.jpg',
                ],
            ];
    
            foreach ($events as $event) {
                Event::create($event);
            }
        }
    }
    ```
    
    **Data yang dibuat:**
    - 3 event dengan berbagai kategori
    - Setiap event memiliki judul, deskripsi, tanggal, lokasi, dan gambar
    

---

1. TicketSeeder
Jalankan pada terminal
    
    ```php
    php artisan make:seeder TicketSeeder
    ```
    
    File: `database/seeders/TicketSeeder.php` 
    Seeder untuk membuat tiket untuk setiap event.
    
    ```php
    <?php
    
    namespace Database\Seeders;
    
    use App\Models\Tiket;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class TicketSeeder extends Seeder
    {
        public function run(): void
        {
            $tickets = [
                [
                    'event_id' => 1,
                    'tipe' => 'premium',
                    'harga' => 1500000,
                    'stok' => 100,
                ],
                [
                    'event_id' => 1,
                    'tipe' => 'reguler',
                    'harga' => 500000,
                    'stok' => 500,
                ],
                [
                    'event_id' => 2,
                    'tipe' => 'premium',
                    'harga' => 200000,
                    'stok' => 300,
                ],
                [
                    'event_id' => 3,
                    'tipe' => 'premium',
                    'harga' => 300000,
                    'stok' => 200,
                ],
            ];
    
            foreach ($tickets as $ticket) {
                Tiket::create($ticket);
            }
        }
    }
    ```
    
    **Data yang dibuat:**
    - Event 1 (Konser): Premium (Rp 1.500.000, stok 100) & Reguler (Rp 500.000, stok 500)
    - Event 2 (Pameran): Premium (Rp 200.000, stok 300)
    - Event 3 (Festival): Premium (Rp 300.000, stok 200)
    

---

1. OrderSeeder
Jalankan pada terminal
    
    ```php
    php artisan make:seeder OrderSeeder
    ```
    
    File: `database/seeders/OrderSeeder.php` 
    Seeder untuk membuat order dan detail order contoh.
    
    ```php
    <?php
    
    namespace Database\Seeders;
    
    use App\Models\DetailOrder;
    use App\Models\Order;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class OrderSeeder extends Seeder
    {
        public function run(): void
        {
            $orders = [
                [
                    'user_id' => 2,
                    'event_id' => 1,
                    'order_date' => '2024-07-01 14:30:00',
                    'total_harga' => 1500000,
                ],
                [
                    'user_id' => 2,
                    'event_id' => 2,
                    'order_date' => '2024-07-02 10:15:00',
                    'total_harga' => 200000,
                ],
            ];
    
            $order_details = [
                [
                    'order_id' => 1,
                    'tiket_id' => 1,
                    'jumlah' => 1,
                    'subtotal_harga' => 1500000,
                ],
                [
                    'order_id' => 2,
                    'tiket_id' => 3,
                    'jumlah' => 1,
                    'subtotal_harga' => 200000,
                ],
            ];
    
            foreach ($orders as $order) {
                Order::create($order);
            }
    
            foreach ($order_details as $detail) {
                DetailOrder::create($detail);
            }
        }
    }
    ```
    
    **Data yang dibuat:**
    - 2 order dari user biasa (user_id: 2)
    - Order 1: Beli 1 tiket premium konser (Rp 1.500.000)
    - Order 2: Beli 1 tiket premium pameran (Rp 200.000)
    

---

## Cara Menjalankan Seeder

Laravel menyediakan **dua cara** untuk menjalankan Database Seeder, yaitu menjalankan **satu seeder tertentu** atau **menjalankan seluruh seeder sekaligus**.

---

1. Menjalankan Satu Seeder
Cara ini digunakan apabila hanya ingin mengisi data pada **tabel tertentu saja** tanpa menjalankan seeder lainnya.

Gunakan perintah berikut sesuai dengan seeder yang ingin dijalankan:
    
    ```bash
    php artisan db:seed --class=CategorySeeder
    php artisan db:seed --class=UserSeeder
    php artisan db:seed --class=EventSeeder
    php artisan db:seed --class=TicketSeeder
    php artisan db:seed --class=OrderSeeder
    
    ```
    

> 📌 Pastikan nama class Seeder sesuai dengan file yang ada di folder database/seeders.
> 

---

1. Menjalankan Semua Seeder Sekaligus
Cara ini digunakan untuk menjalankan **seluruh Seeder secara berurutan** dalam satu perintah.
Buka file berikut:
    
    ```
    database/seeders/DatabaseSeeder.php
    ```
    
    Kemudian sesuaikan isinya seperti berikut:
    
    ```php
    <?php
    
    namespace Database\Seeders;
    
    use App\Models\Kategori;
    use App\Models\User;
    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    
    class DatabaseSeeder extends Seeder
    {
        use WithoutModelEvents;
    
        /**
         * Seed the application's database.
         */
    
        public function run(): void
        {
            $this->call([
                CategorySeeder::class,
                UserSeeder::class,
                EventSeeder::class,
                TicketSeeder::class,
                OrderSeeder::class,
            ]);
        }
    }
    
    ```
    
    Setelah itu, jalankan perintah berikut di terminal:
    
    ```bash
    php artisan db:seed
    ```
    
    User
    
    ![image.png](attachment:da74e3a9-b897-4e5a-a3d6-0ba632948ce0:c3ef86e5-5c8f-443e-bbee-61450be52822.png)
    
    category
    
    ![image.png](attachment:d54066a2-837e-42b5-b29d-9483fa7e4232:image.png)
    
    Event
    
    ![image.png](attachment:0c35c6d4-11bf-47c2-a212-4cd5d21b49fb:image.png)
    
    Ticket
    
    ![image.png](attachment:d82a32ab-e4ff-4add-b638-03b87f967737:image.png)
    
    Order