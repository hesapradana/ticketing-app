# Module 2: Migration dan Model

Dokumentasi lengkap semua Migration dan Model yang digunakan dalam aplikasi ticketing.

---

## 1. Konsep Model dan Migration

Model merupakan salah satu komponen utama dalam konsep **MVC (Model–View–Controller)**.

Model berfungsi sebagai **penghubung antara kode PHP dan tabel di database**.

Sedangkan **Migration** berisi definisi skema yang menentukan struktur dan konfigurasi tabel di database.

**Kesimpulan:**

- **Migration** → Mengatur struktur database
- **Model** → Mengelola data dan logika aplikasi

Keduanya merupakan komponen **wajib** dalam pengembangan aplikasi menggunakan Laravel.

## 2. Konfigurasi Database Menggunakan Migration

Pada tahap awal konfigurasi database, **wajib menggunakan Migration**.

Migration merupakan mekanisme pengelolaan struktur database menggunakan **kode PHP**, bukan melalui phpMyAdmin atau penulisan SQL secara manual.

Penggunaan Migration bertujuan untuk:

- Menjaga **konsistensi struktur database**
- Memudahkan **pelacakan (tracking) perubahan skema**
- Berfungsi sebagai **version control untuk database**
- Mempermudah kolaborasi tim dan proses deployment ke berbagai environment

## 3. Menjalankan Migration

1. Buka **Terminal**.
2. Tambahkan **jendela terminal baru**, lalu pilih **Command Prompt**.
3. Biarkan jendela terminal sebelumnya tetap berjalan untuk menjalankan project.
4. Gunakan terminal baru untuk menjalankan perintah Migration.

![image.png](attachment:0a1816a6-40ac-480b-9cef-9672d3f38ae0:image.png)

File migration yang berisi skema database dapat ditemukan pada folder:

```
database/migrations
```

Untuk menjalankan Migration (memindahkan skema database dari kode PHP ke database), gunakan perintah berikut:

```php
php artisan migrate
```

![image.png](attachment:60e4a284-bfa0-4e12-b497-b0ab84c69c0c:image.png)

## 4. Pembuatan Model dan Migration

Selain tabel **users**, aplikasi Ticketing pada modul ini membutuhkan **5 tabel tambahan**.

Buat file **Model** dan **Migration** dengan menjalankan perintah berikut **secara berurutan**.

Pastikan tidak menukar urutan perintah.

```php
php artisan make:model Kategori -m
php artisan make:model Event -m
php artisan make:model Tiket -m
php artisan make:model Order -m
php artisan make:model DetailOrder -m

```

![image.png](attachment:9183ae1c-b3d5-4741-8e4b-f5d981455474:image.png)

![image.png](attachment:7b1a99f4-c632-4822-a14f-e18b88c17ec6:image.png)

1. Users 

**Migration**
File: `database/migrations/0001_01_01_000000_create_users_table.php`
    
    ```php
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string("no_hp")->nullable();
                $table->enum('role', ['admin', 'user'])->default('user');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
    
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
    
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('users');
            Schema::dropIfExists('password_reset_tokens');
            Schema::dropIfExists('sessions');
        }
    };
    
    ```
    
    **Model** 
    File: `app/Models/User.php`
    
    ```php
    <?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    
    class User extends Authenticatable
    {
        use HasFactory, Notifiable;
    
        protected $fillable = [
            'name',
            'email',
            'password',
            'no_hp',
            'role',
        ];
    
        protected $hidden = [
            'password',
            'remember_token',
        ];
    
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
            ];
        }
    }
    ```
    

---

1. Kategori  

**Migration**
File: `database/migrations/xxxx_create_kategoris_table.php`
    
    ```php
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('kategoris', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->timestamps();
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('kategoris');
        }
    };
    ```
    
    **Model**
    File: `app/Models/Kategori.php`
    
    ```php
    <?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class Kategori extends Model
    {
        use HasFactory;
    
        protected $fillable = [
            'nama',
        ];
    
        public function events()
        {
            return $this->hasMany(Event::class);
        }
    }
    ```
    

---

1. Events 

**Migrations**
File: `database/migrations/xxxx_create_events_table.php`
    
    ```php
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('kategori_id')->constrained()->onDelete('cascade');
                $table->string('judul');
                $table->text('deskripsi');
                $table->string('lokasi');
                $table->dateTime('tanggal_waktu');
                $table->string('gambar')->nullable();
                $table->timestamps();
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('events');
        }
    };
    ```
    
    **Model**
    File: `app/Models/Event.php`
    
    ```php
    <?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class Event extends Model
    {
        use HasFactory;
    
        protected $fillable = [
            'user_id',
            'judul',
            'deskripsi',
            'tanggal_waktu',
            'lokasi',
            'kategori_id',
            'gambar',
        ];
    
        protected $casts = [
            'tanggal_waktu' => 'datetime',
        ];
    
        public function tikets()
        {
            return $this->hasMany(Tiket::class);
        }
    
        public function kategori()
        {
            return $this->belongsTo(Kategori::class);
        }
    
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    }
    ```
    

---

1. Tiket

Migration
File: `database/migrations/xxxx_create_tikets_table.php`
    
    ```php
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('tikets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->enum("tipe", ["reguler", "premium"]);
                $table->decimal("harga", 10, 2);
                $table->integer("stok");
                $table->timestamps();
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('tikets');
        }
    };
    ```
    
    Model
    File: `app/Models/Tiket.php`
    
    ```php
    <?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class Tiket extends Model
    {
        use HasFactory;
    
        protected $fillable = [
            'event_id',
            'tipe',
            'harga',
            'stok',
        ];
    
        public function event()
        {
            return $this->belongsTo(Event::class);
        }
    
        public function detailOrders()
        {
            return $this->hasMany(DetailOrder::class);
        }
    
        public function orders()
        {
            return $this->belongsToMany(Order::class, 'detail_orders')
                ->withPivot('jumlah', 'subtotal_harga');
        }
    }
    ```
    

---

1. Orders

Migration
File: `database/migrations/xxxx_create_orders_table.php`
    
    ```php
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId("event_id")->constrained()->onDelete('cascade');
                $table->dateTime("order_date");
                $table->decimal('total_harga', 10, 2);
                $table->timestamps();
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('orders');
        }
    };
    ```
    
    Model
    File: `app/Models/Order.php`
    
    ```php
    <?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class Order extends Model
    {
        use HasFactory;
    
        protected $casts = [
            'total_harga' => 'decimal:2',
            'order_date' => 'datetime',
        ];
    
        protected $fillable = [
            'user_id',
            'event_id',
            'order_date',
            'total_harga',
        ];
    
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    
        public function tikets()
        {
            return $this->belongsToMany(Tiket::class, 'detail_orders')
                ->withPivot('jumlah', 'subtotal_harga');
        }
    
        public function event()
        {
            return $this->belongsTo(Event::class, 'event_id');
        }
    
        public function detailOrders()
        {
            return $this->hasMany(DetailOrder::class);
        }
    }
    ```
    

---

1. Detail Orders

Migration
File: `database/migrations/xxxx_create_detail_orders_table.php`
    
    ```php
    <?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('detail_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('tiket_id')->constrained()->onDelete('cascade');
                $table->integer('jumlah');
                $table->decimal('subtotal_harga', 10, 2);
                $table->timestamps();
            });
        }
    
        public function down(): void
        {
            Schema::dropIfExists('detail_orders');
        }
    };
    ```
    
    Model 
    File: `app/Models/DetailOrder.php`
    
    ```php
    <?php
    
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    
    class DetailOrder extends Model
    {
        use HasFactory;
    
        protected $fillable = [
            'order_id',
            'tiket_id',
            'jumlah',
            'subtotal_harga',
        ];
    
        public function order()
        {
            return $this->belongsTo(Order::class);
        }
    
        public function tiket()
        {
            return $this->belongsTo(Tiket::class);
        }
    }
    ```
    

---

## Menjalankan Migration

Jalankan Migration menggunakan perintah berikut:

```bash
php artisan migrate
```

Laravel akan secara otomatis menjalankan seluruh file Migration berdasarkan **urutan timestamp** pada nama file.

![image.png](attachment:0e951ffb-6d4a-4a23-b56e-7e8c9454fd65:image.png)

Pada hasil eksekusi Migration di atas, terlihat bahwa **tabel `users` belum mengalami perubahan**. Hal ini terjadi karena tabel tersebut sudah dibuat sebelumnya, sehingga Laravel tidak menjalankan ulang Migration yang telah dieksekusi.

---

## Reset Migration

Untuk mengulang seluruh Migration dari awal (menghapus semua tabel lalu membuat ulang sesuai Migration terbaru), gunakan perintah berikut:

```bash
php artisan migrate:fresh
```

Perintah `migrate:fresh` berfungsi untuk:

- Menghapus **seluruh tabel** di database
- Menjalankan ulang semua file Migration dari awal
- Memastikan struktur database sesuai dengan skema terbaru