# BARENGIN WEB — PROJECT 1 & PROJECT 2

**Mata Kuliah:** Pemrograman Web  
**Nama:** Adika Pamungkas  
**NIM:** 24552011132  
**Kelas:** TIF RP 24B CID  

Repository ini berisi dua project pengembangan web yang dibuat sebagai bagian dari tugas perkuliahan. Kedua project menggunakan PHP sebagai teknologi utama, tetapi memiliki tujuan, arsitektur, dan implementasi yang berbeda.

---

# DAFTAR PROJECT

| Project | Nama | Teknologi Utama | Lokasi Folder |
|---|---|---|---|
| Project 1 | Barengin Website | PHP Native, Bootstrap 5, JavaScript, Laravel REST API | Root Repository |
| Project 2 | CRUD API | PHP Native, PDO, MySQL, Postman | `crud/` |

## Gambaran Umum

### PROJECT 1 — BARENGIN WEBSITE

**Pengembangan Website Barengin yang Terintegrasi dengan Laravel REST API**

Project 1 merupakan pengembangan website Barengin sebagai platform untuk membantu pengguna menemukan aktivitas, bergabung dengan kegiatan bersama, dan membangun interaksi sosial berdasarkan minat yang dimiliki.

Website ini memiliki dua bagian utama, yaitu company profile dan user application. Pengguna dapat melakukan registrasi, login, menjelajahi aktivitas, membuat kegiatan, mengelola profil, serta menggunakan fitur interaksi lainnya.

Frontend dikembangkan menggunakan PHP Native, Bootstrap 5, dan JavaScript. Website terintegrasi dengan Laravel REST API yang juga digunakan oleh aplikasi mobile Barengin.

**Tujuan Project:**
- Mengembangkan antarmuka website Barengin yang responsif dan mudah digunakan.
- Menghubungkan website dengan Laravel REST API sebagai sumber data utama.
- Mengimplementasikan fitur autentikasi dan pengelolaan aktivitas.
- Menghubungkan fitur profil, kategori, favorit, dan join request dengan backend.
- Menjaga konsistensi data antara website dan aplikasi mobile melalui API yang sama.

**Teknologi yang Digunakan:**
- PHP Native 8.2+ (disarankan PHP 8.4).
- Bootstrap 5.3.
- HTML5 dan CSS3.
- Vanilla JavaScript.
- Laravel REST API.
- MySQL/MariaDB sebagai database yang dikelola oleh Laravel.

**Fitur Utama:**
- Company profile dan halaman pengenalan Barengin.
- Registrasi, login, dan logout.
- Explore aktivitas berdasarkan kategori.
- Melihat detail aktivitas.
- Membuat, mempublikasikan, dan membatalkan aktivitas.
- Mengirim dan mengelola join request.
- Mengelola aktivitas sebagai host maupun member.
- Menambahkan dan menghapus favorit.
- Mengelola profil dan minat pengguna.
- Fitur pelaporan dan pemblokiran pengguna.

**Arsitektur Sistem:**

```text
Pengguna
   |
   v
Browser (Chrome)
   |
   v
PHP Website
(localhost:8080)
   |
   v
PHP API Client
(Session + CSRF)
   |
   v
Laravel REST API
(127.0.0.1:8000/api)
   |
   v
MySQL / MariaDB
(barengin_db)
```

Website tidak melakukan koneksi langsung ke MySQL untuk fitur Barengin. Seluruh komunikasi data dilakukan melalui Laravel REST API agar validasi, autentikasi, dan business logic tetap dikelola oleh backend.

**Lokasi Project 1:**

Project 1 berada di direktori utama repository ini. File `index.php` merupakan entry point website.

Folder penting:
- `api/` — HTTP client dan service wrapper untuk komunikasi dengan Laravel API.
- `assets/` — CSS, JavaScript, gambar, dan aset antarmuka.
- `config/` — Konfigurasi website dan alamat API.
- `includes/` — Helper, session, action handler, dan komponen layout.
- `pages/` — Halaman company profile dan aplikasi Barengin.
- `Barengin_Backend/` — Source code Laravel REST API, dokumentasi endpoint, migration, dan pengujian.

### PROJECT 2 — CRUD API

**Implementasi Create, Read, Update, dan Delete Menggunakan PHP Native**

Project 2 merupakan implementasi konsep CRUD (Create, Read, Update, Delete) untuk mengelola data aktivitas pada database MySQL.

Project ini dibuat secara terpisah dari integrasi Laravel REST API pada Project 1. Implementasi menggunakan PHP Native dengan PDO sebagai penghubung database, kemudian diuji menggunakan Postman untuk memastikan setiap operasi CRUD dapat berjalan dengan baik.

**Tujuan Project:**
- Memahami proses komunikasi antara PHP dan database MySQL.
- Mengimplementasikan operasi Create, Read, Update, dan Delete.
- Membuat endpoint API sederhana menggunakan PHP Native.
- Menguji request dan response API menggunakan Postman.
- Memastikan proses pengelolaan data berjalan sesuai dengan hasil yang diharapkan.

**Teknologi yang Digunakan:**
- PHP Native.
- PDO (PHP Data Objects).
- MySQL/MariaDB.
- XAMPP/Laragon.
- Postman.

**Operasi CRUD yang Diimplementasikan:**

| Operasi | HTTP Method | Keterangan |
|---|---|---|
| Create | POST | Menambahkan data aktivitas baru. |
| Read | GET | Menampilkan seluruh data atau detail berdasarkan ID. |
| Update | PATCH | Memperbarui data aktivitas berdasarkan ID. |
| Delete | DELETE | Menghapus data aktivitas berdasarkan ID. |

**Struktur File Project 2:**

Seluruh implementasi Project 2 berada di dalam folder `crud/`, bukan di root repository.

```text
crud/
├── connection.php
├── get_data.php
├── insup_data.php
├── delete_data.php
├── README.md
└── Screenshots/
    ├── 01-laragon-running.png.png
    ├── 02-phpmyadmin.png.png
    ├── 03-database-structure.png.png
    ├── 04-1-target-table.png.png
    ├── 04-2-detail-target-table.png.png
    ├── 05-crud-api-files.png.png
    ├── 06-connection-test.png.png
    ├── 07-get-all-activities.png.png
    ├── 08-get-activity-detail.png.png
    ├── 09-1-post-headers.png.png
    ├── 09-2-post-body.png.png
    ├── 09-3-post-success.png.png
    ├── 10-1-patch-headers.png.png
    ├── 10-2-patch-body.png.png
    ├── 10-3-patch-success.png.png
    ├── 11-1-delete-headers.png.png
    ├── 11-2-delete-request.png.png
    ├── 11-3-delete-success.png.png
    ├── 12-1-verify-deleted-id.png.png
    └── 12-2-verify-all-activities.png.png
```

**Penjelasan File:**
- `connection.php` — Mengatur koneksi PHP dengan database menggunakan PDO.
- `get_data.php` — Menangani request GET untuk mengambil data aktivitas.
- `insup_data.php` — Menangani request POST dan PATCH untuk menambah serta memperbarui data.
- `delete_data.php` — Menangani request DELETE untuk menghapus data.
- `README.md` — Dokumentasi khusus pengerjaan CRUD.
- `Screenshots/` — Bukti dokumentasi proses konfigurasi, implementasi, dan pengujian API.

**Database:**

- Nama database: `barengin_db`
- Tabel yang digunakan: `activities`
- Primary key: `id`

Project ini menggunakan database MySQL yang dijalankan melalui XAMPP/Laragon. Koneksi database dilakukan secara langsung melalui PDO.

**Alamat Pengujian Lokal:**

Base URL:

```text
http://localhost:8080/crud/
```

Contoh endpoint:

```text
GET    http://localhost:8080/crud/get_data.php
POST   http://localhost:8080/crud/insup_data.php
PATCH  http://localhost:8080/crud/insup_data.php?id=ID
DELETE http://localhost:8080/crud/delete_data.php?id=ID
```

Pengujian dilakukan menggunakan Postman, termasuk pemeriksaan hasil penambahan, perubahan, dan penghapusan data.

**Dokumentasi Pengujian:**

Bukti pengerjaan setiap tahap dapat dilihat pada folder `crud/Screenshots/`. Screenshot disusun berdasarkan urutan proses, mulai dari persiapan server dan database hingga pengujian seluruh operasi CRUD.

Untuk penjelasan teknis lebih lanjut, silakan buka [`crud/README.md`](crud/README.md).

---

# STRUKTUR UTAMA REPOSITORY

Berikut struktur direktori utama repository Barengin-Web:

```text
Barengin_Web/
│
├── api/                    # API client Project 1
├── assets/                 # CSS, JavaScript, dan aset
├── config/                 # Konfigurasi website
├── crud/                   # PROJECT 2: CRUD API
│   ├── connection.php
│   ├── get_data.php
│   ├── insup_data.php
│   ├── delete_data.php
│   ├── README.md
│   └── Screenshots/
│
├── docs/                   # Dokumentasi integrasi website
├── includes/               # Helper dan komponen layout
├── pages/                  # Halaman website Project 1
├── tools/                  # Utilitas pemeriksaan API
│
├── Barengin_Backend/       # Laravel REST API
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── docs/
│   ├── routes/
│   └── tests/
│
├── index.php               # Entry point Project 1
├── .gitignore
└── README.md
```

**Catatan:** Project 1 dan Project 2 disimpan dalam satu repository, tetapi implementasi dan tujuan pengerjaannya tetap terpisah. Project 1 menggunakan Laravel REST API sebagai backend, sedangkan Project 2 menggunakan PHP Native dan PDO untuk mengakses database secara langsung.

---

# PANDUAN MENJALANKAN PROJECT

## A. Menjalankan Project 1 — Barengin Website

Project 1 membutuhkan dua proses server yang berjalan bersamaan.

**1. Menjalankan Laravel API**

Buka terminal pertama dan masuk ke folder backend:

```powershell
cd "C:\Users\ASUS\Documents\Barengin\Barengin_Web\Barengin_Backend"
```

Pastikan konfigurasi `.env` Laravel sudah disesuaikan dengan database lokal. Jalankan:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Tes API melalui browser:

```text
http://127.0.0.1:8000/api/health
```

**2. Menjalankan Website PHP**

Buka terminal kedua dan masuk ke root repository:

```powershell
cd "C:\Users\ASUS\Documents\Barengin\Barengin_Web"
```

Jalankan server PHP:

```powershell
php -S localhost:8080
```

Buka website melalui browser:

```text
http://localhost:8080
```

**3. Konfigurasi API**

Konfigurasi default website mengarah ke:

```text
http://127.0.0.1:8000/api
```

Jika alamat API berbeda, salin file:

```text
config/api.local.example.php
```

Menjadi:

```text
config/api.local.php
```

Kemudian sesuaikan nilai `base_url`. File konfigurasi lokal tersebut tidak perlu diunggah ke repository.

## B. Menjalankan Project 2 — CRUD API

**1. Menyiapkan server dan database**

- Jalankan Apache dan MySQL melalui XAMPP atau Laragon.
- Pastikan database `barengin_db` tersedia.
- Pastikan tabel `activities` sudah tersedia di dalam database.

**2. Menjalankan server PHP**

Pastikan server PHP berjalan pada port 8080 dan root directory mengarah ke repository `Barengin_Web`.

**3. Mengakses endpoint CRUD**

Buka Postman dan gunakan base URL:

```text
http://localhost:8080/crud/
```

Pilih HTTP method sesuai operasi yang ingin diuji.

**4. Melakukan pengujian**

Lakukan pengujian GET, POST, PATCH, dan DELETE. Periksa response API serta perubahan data pada database.

Dokumentasi pengujian dapat dilihat di:

```text
crud/Screenshots/
```

---

# KEAMANAN DAN KONFIGURASI

Beberapa hal yang perlu diperhatikan ketika menjalankan atau mengembangkan project:

- File `.env` Laravel tidak boleh diunggah ke repository publik.
- File `config/api.local.php` digunakan untuk konfigurasi lokal dan diabaikan oleh Git.
- Token autentikasi website disimpan di PHP session server-side.
- Request POST dilindungi dengan CSRF token.
- Project 1 menggunakan Laravel API untuk pengelolaan data Barengin.
- Project 2 menggunakan koneksi PDO secara langsung untuk kebutuhan CRUD.
- Kredensial database dan API key tidak boleh dimasukkan ke repository publik.
- Konfigurasi pengembangan lokal perlu disesuaikan sebelum digunakan pada server production.

---

# INFORMASI REPOSITORY

**Repository:** Barengin-Web  
**Platform:** GitHub  
**Branch:** `main`  
**Pemilik:** Alpmngks310  

Repository ini digunakan untuk menyimpan source code, dokumentasi, dan bukti pengerjaan Project 1 dan Project 2 mata kuliah Pemrograman Web.

**GitHub Repository:**  
https://github.com/Alpmngks310/Barengin-Web

---

**Dibuat oleh:**  
Adika Pamungkas  
24552011132  
TIF RP 24B CID
