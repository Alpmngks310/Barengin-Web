# Barengin Web UTS — PHP Website + Laravel API

Frontend company profile dan user application Barengin berbasis PHP native, Bootstrap 5, dan JavaScript. Backend website adalah **PHP API client** yang berkomunikasi dengan Laravel REST API milik Barengin Mobile. Website **tidak** membuat koneksi langsung ke MySQL dan tidak menggantikan business logic Laravel.

## Teknologi

- PHP website: 8.2+ (disarankan PHP 8.4)
- Laravel Mobile API yang disertakan sebelumnya mensyaratkan PHP ^8.3 (gunakan PHP 8.4 untuk menjalankan keduanya)
- Bootstrap 5.3
- Vanilla JavaScript
- Laravel REST API yang sudah digunakan Barengin Mobile
- MySQL/MariaDB dikelola oleh Laravel API

## Struktur project

```text
Barengin_Web/
├── api/                    # HTTP client dan service wrapper untuk Laravel API
├── assets/                 # CSS, JavaScript, images, fonts
├── config/                 # Konfigurasi API
├── crud/                   # Placeholder modul CRUD UTS terpisah
├── docs/                   # Catatan integrasi dan troubleshooting
├── includes/               # Bootstrap/session, helper, action handlers, layout
├── pages/                  # Company profile dan halaman aplikasi
├── tools/                  # Utilitas pemeriksaan koneksi API
├── index.php
├── .gitignore
└── README.md
```

## Arsitektur

```text
Chrome
  │
  ▼
PHP Website (localhost:8080)
  │  PHP API Client + Session + CSRF
  ▼
Laravel REST API (127.0.0.1:8000/api)
  │  Auth, validation, policies, business rules
  ▼
MySQL/MariaDB: barengin_db
```

Token Sanctum disimpan di PHP session server-side. Token tidak dikirim ke browser JavaScript/localStorage. Website hanya berkomunikasi dengan API Laravel; jangan menambahkan koneksi PDO/MySQL langsung untuk fitur Barengin karena itu akan melewati validasi dan aturan bisnis di Laravel.

## 1. Persiapan Laravel API

Pastikan project Laravel Barengin Mobile sudah berjalan dan `.env` Laravel menunjuk ke database `barengin_db` yang benar. Untuk konfigurasi MySQL, nilai umum yang perlu diperiksa di `.env` Laravel adalah:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barengin_db
DB_USERNAME=...
DB_PASSWORD=...
```

Isi username/password sesuai konfigurasi MySQL lokal kamu. **Jangan masukkan `.env` Laravel ke repository public.** Jangan mengimpor ulang SQL dump jika database yang sama sudah terisi karena dapat menimpa/duplikasi data.

Buka terminal pertama di folder Laravel Barengin yang sudah ada:

```powershell
cd "PATH\KE\Barengin_Backend"
php artisan serve --host=127.0.0.1 --port=8000
```

Tes endpoint API di Chrome:

```text
http://127.0.0.1:8000/api/health
```

Respons yang diharapkan memiliki `success: true` dan pesan bahwa Barengin API berjalan.

## 2. Konfigurasi website PHP

Default konfigurasi sudah diarahkan ke:

```text
http://127.0.0.1:8000/api
```

Jika alamat API kamu berbeda, salin `config/api.local.example.php` menjadi `config/api.local.php`, lalu ubah `base_url`. File `api.local.php` sudah masuk `.gitignore`.

Contoh:

```php
<?php
return [
    'base_url' => 'http://127.0.0.1:8000/api',
    'timeout' => 20,
    'device_name' => 'barengin-web',
    'app_timezone' => 'Asia/Jakarta',
    'demo_fallback' => false,
];
```

Tidak diperlukan `composer install` untuk backend website ini. PHP menggunakan HTTP client ringan bawaan; cURL dipakai jika tersedia dan PHP streams menjadi fallback.

## 3. Jalankan frontend website

**Gunakan terminal kedua.** Folder website ini berbeda proses/port dari Laravel API. Karena port 8000 dipakai Laravel, jalankan website pada port 8080.

```powershell
cd "C:\Users\ASUS\Documents\Barengin\Barengin_Web"
php -S localhost:8080
```

Buka Chrome:

```text
http://localhost:8080
```

Jika menggunakan Laragon Apache Virtual Host, website juga dapat diakses melalui domain lokal yang kamu buat, tetapi pastikan `config/api.php` tetap mengarah ke port Laravel API.

## 4. Login dan registrasi

- Login menggunakan akun yang sudah terdaftar di database Barengin.
- Registrasi website mengirim `name`, `username` (opsional), `email`, `password`, `password_confirmation`, konfirmasi usia 18+, dan persetujuan terms/privacy ke `/api/auth/register`.
- Setelah berhasil, token Bearer dari Laravel disimpan pada session PHP.
- Logout memanggil `/api/auth/logout` lalu menghapus session lokal.

Tidak ada akun demo hardcoded pada versi integrasi ini.

## Fitur yang dihubungkan

| Website | Endpoint Laravel |
|---|---|
| Health check | `GET /api/health` |
| Register / Login / Logout | `/api/auth/register`, `/api/auth/login`, `/api/auth/logout` |
| User session | `GET /api/me` |
| Kategori | `GET /api/categories` |
| Explore / Activity detail | `GET /api/activities`, `GET /api/activities/{id}` |
| Create / Publish / Cancel | `POST /api/activities`, `POST /api/activities/{id}/publish`, `POST /api/activities/{id}/cancel` |
| Host / Member activities | `GET /api/my/activities?role=host|member` |
| Join request | `POST /api/activities/{id}/join-requests` |
| Request list / decision / cancel | `/api/my/join-requests`, `/api/activities/{id}/join-requests`, `/api/join-requests/{id}/decision` |
| Favorites | `/api/my/favorites`, `POST/DELETE /api/activities/{id}/favorite` |
| Profile / interests | `GET/PATCH /api/profile`, `PUT /api/profile/interests` |
| Safety | `/api/reports`, `/api/my/blocked-users`, `/api/users/{id}/block` |

Detail request/response dan aturan validasi mengikuti `docs/ENDPOINTS.md`, `docs/DATABASE_MAPPING.md`, serta FormRequest/Resource pada source Laravel mobile yang diberikan. Website mematuhi batas minat 3–6 kategori dan mengirim payload aktivitas sesuai validasi Laravel.

## Database compatibility

Website mengikuti resource Laravel, bukan membaca tabel secara langsung. Field penting yang dipetakan antara lain:

- `activities`: `category_id`, `participant_level`, `starts_at`, `ends_at`, `timezone`, lokasi, `capacity`, `budget_amount`, dan `status`.
- `categories`: `id`, `name`, `slug`, `description`, `icon_key`.
- `users` + `user_profiles`: data akun dan profil terpisah.
- `activity_favorites`, `activity_participants`, `join_requests`: relasi interaksi pengguna.
- `user_interests`, `user_blocks`, `user_reports`: minat dan safety.

Saat membuat aktivitas, frontend meminta tanggal, waktu mulai/selesai, venue, alamat, kota, kapasitas, dan budget. Backend PHP mengubah tanggal/waktu menjadi ISO-8601 dengan zona waktu `Asia/Jakarta`. Laravel tetap memvalidasi bahwa waktu mulai di masa depan, waktu selesai setelah mulai, kapasitas 2–15 (termasuk Host), dan kategori aktif. Aktivitas dibuat sebagai `draft`, kemudian website memanggil endpoint `publish` agar bisa tampil di Explore.

## Keamanan yang diterapkan

- Token Bearer hanya disimpan di PHP session server-side.
- Cookie session memakai `HttpOnly`, `SameSite=Lax`, dan `Secure` saat HTTPS.
- Token tidak ditaruh di HTML, URL, localStorage, atau JavaScript.
- Form POST dilindungi CSRF token.
- Output HTML di-escape dengan `htmlspecialchars`.
- Login/register menggunakan HTTPS pada deployment publik (gunakan reverse proxy/SSL saat hosting).
- Tidak ada file SQL database, `.env` Laravel, atau secret mobile dalam project website.

## Cek API dari terminal

```powershell
php tools/check-api.php
```

Script ini memanggil `GET /api/health` berdasarkan konfigurasi `config/api.php`.

## Catatan fitur dan batasan integrasi

- API yang ada menjadi sumber data utama. Jika Laravel API mati/tidak dapat diakses, halaman app menampilkan pesan koneksi, bukan mengubah diam-diam menjadi data palsu.
- Kategori, aktivitas, favorit, join request, profil, interests, report, dan block diambil/disimpan lewat Laravel API.
- Host bisa mempublikasikan draft dan membatalkan aktivitas; halaman Activity menampilkan kontrol tersebut untuk pemilik aktivitas.
- Modul CRUD mata kuliah Project 2 tetap terpisah dari alur Barengin API dan akan disiapkan sesuai instruksi tugas CRUD.
- Fungsi yang belum disediakan endpoint Laravel (misalnya reset password atau notifikasi) tidak diklaim aktif.

## Troubleshooting

**`Tidak dapat menghubungi Laravel API`**
1. Pastikan terminal Laravel masih berjalan pada port 8000.
2. Buka `http://127.0.0.1:8000/api/health`.
3. Periksa `base_url` pada `config/api.php` atau `config/api.local.php`.
4. Pastikan port 8000 tidak sedang dipakai proses lain.

**Website tidak terbuka**
1. Pastikan terminal PHP website menjalankan port 8080.
2. Cek `php -v` dan pastikan PHP Laragon masuk PATH.
3. Jika port 8080 terpakai, ganti misalnya menjadi 8081 dan buka port yang sama di Chrome.

**Login ditolak**
- Pastikan akun sudah terdaftar/aktif pada database yang digunakan Laravel.
- Pastikan API Laravel terhubung ke database `barengin_db` yang benar.
- Validasi dan pesan error dari Laravel akan ditampilkan kembali di website.

**Upload avatar gagal**
- Periksa `upload_max_filesize`/`post_max_size` PHP dan permission `storage` Laravel.
- Pastikan Laravel sudah menjalankan `php artisan storage:link` agar URL file publik dapat diakses.
- Format yang diterima API: JPG, JPEG, PNG, WEBP; maksimum 5 MB.

## Public repository checklist

Jangan commit `config/api.local.php`, `.env`, dump SQL, token, log, atau kredensial. File `config/api.local.example.php` aman sebagai contoh konfigurasi lokal. Periksa `.gitignore` sebelum push ke GitHub.
