# **Module 4: Authentication System**

Modul ini akan setup authentication dengan role (admin & user).

---

Setelah proses instalasi Laravel selesai dan konfigurasi dasar aplikasi berhasil dilakukan, langkah selanjutnya adalah menyiapkan **sistem autentikasi** agar aplikasi dapat digunakan oleh pengguna secara aman.

Laravel menyediakan paket **Laravel Breeze** sebagai solusi autentikasi yang **sederhana, ringan, dan cepat diimplementasikan**. Laravel Breeze menyediakan fitur dasar seperti **Login**, **Register**, **Logout**, serta manajemen pengguna dengan struktur yang sudah terstandarisasi.

Pada bagian ini akan dibahas proses **instalasi Laravel Breeze** hingga autentikasi dapat digunakan pada aplikasi.

## 1. Instalasi Laravel Breeze

Jalankan perintah berikut pada terminal untuk menginstal Laravel Breeze:

```bash
composer require laravel/breeze --dev
```

![image.png](attachment:d1cfdf7e-3fc6-4c3c-87d3-b26e6d7c6c3e:image.png)

Setelah itu, jalankan perintah instalasi Breeze:

```bash
php artisan breeze:install
```

![image.png](attachment:b3a21e4f-f239-4694-bad6-eb3cec1473fc:image.png)

## 2. Install Dependency Frontend

Laravel Breeze menggunakan **NPM** untuk membangun aset frontend (CSS & JavaScript) agar tampilan dan interaksi autentikasi dapat berjalan dengan baik.

Jalankan perintah berikut:

```bash
npm install
```

Setelah proses instalasi selesai, jalankan build frontend dengan perintah:

```bash
npm run build
```

> 📸 Screenshot npm run build
> 

## 3. Verifikasi Tampilan Autentikasi

Refresh tampilan project pada browser.

Pada bagian **kanan atas halaman**, akan muncul dua menu baru:

- **Login**
- **Register**

![image.png](attachment:37d481a6-b1cd-4a7a-924d-a3d1edd36de6:image.png)

## 4. Login Menggunakan Akun Seeder

Gunakan akun yang telah dibuat sebelumnya melalui **UserSeeder** untuk melakukan login.

**Credential Login:**

- **Email**: `admin@gmail.com`
- **Password**: `password`

![image.png](attachment:31e4657a-1a4a-4da1-9f7e-b4e629e60868:image.png)

Jika berhasil maka hasilnya akan muncul tampilan berikut

![image.png](attachment:f41e51fb-2901-447a-85f8-94d8efdfbbd7:image.png)

## 5. Logout Akun

Setelah berhasil login dan melakukan verifikasi, **logout terlebih dahulu** sebelum melanjutkan ke tutorial berikutnya.