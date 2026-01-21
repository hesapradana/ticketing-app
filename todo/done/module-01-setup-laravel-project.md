# **Module 1: Setup Laravel Project**

Modul ini akan membantu setup Laravel project dari awal sampai siap development.

---

## **1. Install Laravel**

Buat project Laravel baru:

```bash
composer create-project laravel/laravel ticketing_app
cd ticketing_app
```

![image.png](attachment:495f33cd-c015-400e-a02f-91420e629ebc:image.png)

### **2. Konfigurasi Database**

Buka project yang sudah dibuat kemudian edit file `.env` untuk koneksi database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketing_app
DB_USERNAME=root
DB_PASSWORD=
```

![image.png](attachment:6f1e20bc-2092-4006-bc3d-6fc866ee1e68:image.png)

### 3. Migrate Database

Jalankan perintah pada terminal untuk create database dan migrate:

```markdown
php artisan migrate
```

jika muncul seperti ini 

![image.png](attachment:cb0670e3-0fb2-462e-8a47-75e9c11aafb7:image.png)

ketik yes lalu enter, kemudian laravel akan otomatis membuat database `ticketing_app`

![image.png](attachment:c45e4af5-09f6-42ff-9285-4219c1a2fa63:image.png)

### **4. Jalankan Server**

Jalankan development server pada terminal:

```bash
php artisan serve
```

Buka browser ke `http://localhost:8000`