# BARENGIN — Laravel REST API Backend

Backend API untuk aplikasi mobile Barengin. Project ini disiapkan untuk **Laravel 13 + PHP 8.3+ + Laravel Sanctum 4.x + MySQL `barengin_db`**.

> Penting: folder ini berisi source project Laravel yang siap dipasang dependensinya. Folder `vendor/` tidak disertakan. Jalankan `composer install` di laptopmu sebelum menjalankan Artisan.

## 1. Kesesuaian dengan database yang sudah di-import

Database yang diharapkan: `barengin_db`.

Backend ini menggunakan nama tabel/kolom yang sama dengan SQL package Barengin Database v1.0:
- users, user_profiles, categories, user_interests
- activities, activity_favorites
- join_requests, activity_participants
- user_blocks, user_reports

**Jangan menjalankan `migrate:fresh`, `db:wipe`, atau migration yang membuat ulang tabel inti.** Tabel inti sudah kamu import melalui phpMyAdmin.

Project ini menyertakan satu migration aplikasi untuk `personal_access_tokens` dengan struktur yang kompatibel dengan Laravel Sanctum. Saat `php artisan migrate` dijalankan, Laravel membuat tabel `migrations` dan `personal_access_tokens`. Tidak ada migration project yang membuat ulang 10 tabel inti.

## 2. Persyaratan

- PHP 8.3 atau lebih baru (PHP 8.4 yang kamu pasang sebelumnya memenuhi versi).
- Composer 2.2+.
- Ekstensi PHP: ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pdo, pdo_mysql, session, tokenizer, xml.
- MySQL/MariaDB dari XAMPP.
- Database `barengin_db` sudah berhasil di-import.

Periksa CLI:
```powershell
php -v
php --ini
php -m
composer --version
```

Pastikan `php -m` menampilkan `pdo_mysql` dan `mbstring`. Kalau belum, aktifkan extension pada `php.ini` yang benar-benar digunakan terminal.

## 3. Instalasi di Windows (PowerShell)

Ekstrak ZIP sehingga folder berisi `composer.json`, `artisan`, `app`, `routes`, dan `bootstrap`.

Pindah ke folder backend:
```powershell
cd path\ke\Barengin_Backend
```

Install dependency:
```powershell
composer install
```

Buat file environment:
```powershell
Copy-Item .env.example .env
```

Generate application key:
```powershell
php artisan key:generate
```

Pastikan `.env`:
```env
APP_NAME=Barengin
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=UTC

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barengin_db
DB_USERNAME=root
DB_PASSWORD=
```
Sesuaikan username dan password MySQL sesuai XAMPP milikmu.

Bersihkan cache config dan periksa koneksi:
```powershell
php artisan config:clear
php artisan about
php artisan migrate:status
```

Jalankan migration paket (terutama personal access token Sanctum):
```powershell
php artisan migrate
```

**Jangan gunakan `php artisan migrate:fresh`.** Itu akan menghapus database dan tidak sesuai untuk database yang sudah berisi skema Barengin.

Buat symbolic link untuk file publik:
```powershell
php artisan storage:link
```

Jalankan API:
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

## 4. Tes API pertama

Buka:
- `http://127.0.0.1:8000/up` — health Laravel.
- `http://127.0.0.1:8000/api/health` — health JSON Barengin.
- `http://127.0.0.1:8000/api/categories` — daftar kategori.

Contoh respons:
```json
{
  "success": true,
  "message": "Barengin API is running.",
  "data": {
    "app": "Barengin",
    "environment": "local",
    "time": "..."
  }
}
```

Jalankan:
```powershell
php artisan route:list --path=api
php artisan test
```

## 5. Alamat API untuk Flutter

- Android Emulator: `http://10.0.2.2:8000/api`
- Flutter Web/Desktop pada laptop yang sama: `http://127.0.0.1:8000/api`
- HP fisik: `http://IP-LAN-LAPTOP:8000/api` (HP dan laptop di jaringan yang sama; firewall harus mengizinkan port 8000).

Jangan menggunakan `localhost` dari Android Emulator untuk mengakses server laptop.

Untuk Android development, pastikan konfigurasi internet permission tersedia di `android/app/src/main/AndroidManifest.xml`. Android versi baru membatasi cleartext HTTP; gunakan konfigurasi cleartext khusus debug atau HTTPS. Jangan mengaktifkan cleartext secara global untuk rilis produksi.

## 6. Autentikasi untuk Flutter

Register dan login mengembalikan token Sanctum. Simpan token dengan secure storage pada Flutter dan kirim pada endpoint privat:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

Jangan menyimpan token di SharedPreferences biasa untuk rilis produksi. Gunakan secure storage.

### Register
`POST /api/auth/register`
```json
{
  "name": "Adika Pamungkas",
  "username": "adika",
  "email": "adika@example.com",
  "password": "StrongPass123",
  "password_confirmation": "StrongPass123",
  "adult_confirmed": true,
  "terms_accepted": true,
  "privacy_accepted": true,
  "device_name": "Adika Android"
}
```

Ketiga consent wajib true. `adult_confirmed` adalah self-attestation untuk aturan MVP 18+, bukan verifikasi identitas independen.

### Login
`POST /api/auth/login`
```json
{
  "email": "adika@example.com",
  "password": "StrongPass123",
  "device_name": "Adika Android"
}
```

### Logout
`POST /api/auth/logout` (Bearer token).

### Pengguna saat ini
`GET /api/me` (Bearer token).

## 7. Daftar endpoint

Semua endpoint memakai prefix `/api`.

### Public
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/health` | Status API |
| POST | `/auth/register` | Registrasi |
| POST | `/auth/login` | Login |
| GET | `/categories` | Daftar kategori |
| GET | `/activities` | Explore dan filter aktivitas |
| GET | `/activities/{id}` | Detail aktivitas yang dipublikasikan |

### Authenticated
| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/auth/logout` | Logout token saat ini |
| GET | `/me` | Data akun saat ini |
| GET | `/profile` | Profil |
| PATCH | `/profile` | Ubah profil; avatar gunakan multipart/form-data |
| PUT | `/profile/interests` | Ganti minat (minimal 3, maksimal 6) |
| GET | `/my/activities` | Aktivitas Host/Member |
| GET | `/my/join-requests` | Permintaan yang dikirim pengguna |
| DELETE | `/my/join-requests/{id}` | Batalkan request Pending milik sendiri |
| GET | `/my/favorites` | Aktivitas favorit |
| POST | `/activities` | Buat draft aktivitas |
| PATCH | `/activities/{id}` | Ubah draft/published milik sendiri sebelum mulai |
| POST | `/activities/{id}/publish` | Publikasikan draft |
| POST | `/activities/{id}/cancel` | Batalkan aktivitas |
| POST | `/activities/{id}/leave` | Member keluar dari aktivitas |
| POST | `/activities/{id}/start` | Mulai aktivitas (Published → Ongoing) |
| GET | `/activities/{id}/participants` | Host melihat peserta aktif |
| DELETE | `/activities/{id}/participants/{userId}` | Host mengeluarkan Member |
| POST | `/activities/{id}/complete` | Tandai ongoing sebagai completed |
| POST | `/activities/{id}/join-requests` | Ajukan join request |
| GET | `/activities/{id}/join-requests` | Host melihat request |
| PATCH | `/join-requests/{id}/decision` | Host menerima/menolak |
| POST | `/activities/{id}/favorite` | Simpan favorit |
| DELETE | `/activities/{id}/favorite` | Hapus favorit |
| POST | `/reports` | Kirim laporan |
| GET | `/my/blocked-users` | Daftar akun diblokir |
| POST | `/users/{id}/block` | Blokir pengguna |
| DELETE | `/users/{id}/block` | Buka blokir |

## 8. Parameter Explore

`GET /api/activities`

Parameter opsional:
- `q`: kata kunci judul/deskripsi/tempat/kota.
- `category_id` atau `category_slug`.
- `city`, `area`.
- `date_from`, `date_to` (YYYY-MM-DD).
- `budget_max` (nominal IDR).
- `participant_level`: beginner / all_levels / experienced.
- `latitude`, `longitude`, `radius_km` (keduanya latitude dan longitude wajib dikirim bersama).
- `per_page` (1–50).

GPS pengguna hanya dipakai sebagai parameter pencarian sementara; backend tidak menyimpan live location tersebut.

## 9. Create activity

`POST /api/activities` membuat draft dan otomatis mendaftarkan pembuat sebagai Host di `activity_participants`. Kapasitas 2–15 **termasuk Host**.

Contoh:
```json
{
  "category_id": 1,
  "title": "Badminton Weekend",
  "description": "Main badminton santai untuk pemula dan semua level.",
  "participant_level": "all_levels",
  "starts_at": "2026-12-12T01:00:00Z",
  "ends_at": "2026-12-12T03:00:00Z",
  "timezone": "Asia/Jakarta",
  "venue_name": "GOR Example",
  "address": "Jalan Example No. 1",
  "area": "Dago",
  "city": "Bandung",
  "province": "Jawa Barat",
  "country_code": "ID",
  "latitude": -6.895,
  "longitude": 107.613,
  "capacity": 8,
  "budget_amount": 30000,
  "currency": "IDR"
}
```

Tanggal dikirim sebagai ISO-8601 dengan offset/timezone. Konvensi database backend adalah UTC, sedangkan `timezone` dipakai untuk tampilan lokal.

Setelah membuat draft:
`POST /api/activities/{id}/publish`

## 10. Join request dan approval

### Member mengajukan
`POST /api/activities/{id}/join-requests`
```json
{ "message": "Halo, saya tertarik ikut aktivitas ini." }
```
Status awal selalu `pending`.

### Host menyetujui/menolak
`PATCH /api/join-requests/{id}/decision`
```json
{ "status": "accepted", "decision_note": "Sampai ketemu!" }
```
Atau:
```json
{ "status": "rejected", "decision_note": "Kuota sudah diprioritaskan untuk peserta lain." }
```

Approval diproses dalam transaksi database dan mengunci baris Activity sebelum menghitung kapasitas. Jika kapasitas penuh, request tidak diubah menjadi Accepted. Jumlah peserta aktif menghitung Host.

Satu pasangan activity-user menggunakan satu baris join request. Jika status sebelumnya `rejected`, Member boleh mengirim ulang; backend mengembalikan baris itu ke `pending`. Request `pending` atau `accepted` tidak dapat diduplikasi.

## 11. Kontrak response

Response sukses:
```json
{
  "success": true,
  "message": "Aktivitas berhasil dimuat.",
  "data": {},
  "meta": {}
}
```

Response validasi (422):
```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

Semua tanggal waktu dari API menggunakan ISO-8601.

## 12. Aturan database yang ditangani Laravel

SQL menjaga tipe, foreign key, unique key dan sebagian CHECK constraints. Laravel menjaga aturan lintas tabel:
- Host tidak boleh join aktivitas sendiri.
- Host hanya dapat memproses permintaan aktivitas miliknya.
- Kapasitas dicek di dalam DB transaction dan `lockForUpdate`.
- User yang saling memblokir tidak dapat berinteraksi melalui join.
- Aktivitas non-published / sudah mulai / cancelled / completed tidak menerima join baru.
- Minimum tiga minat harus dipilih.
- Self-block dan self-report ditolak.

## 13. Catatan keamanan dan status

- Token Sanctum disimpan di `personal_access_tokens` dengan hash oleh Sanctum.
- Password disimpan melalui hashing Laravel.
- Endpoint privat memakai `auth:sanctum`.
- CORS `*` hanya untuk local development. Batasi `FRONTEND_ORIGINS` sebelum production.
- API belum memiliki panel Admin untuk memoderasi laporan; report disimpan dengan status `pending`.
- Badge “verified” belum boleh ditampilkan karena MVP belum memiliki alur verifikasi identitas.
- File upload avatar dibatasi image 5 MB dan disimpan pada public disk. Untuk deployment, pastikan file validation dan storage permission benar.

## 14. Database migrations: baca ini sebelum menjalankan

Project tidak menyertakan migration untuk `users`, `activities`, dan sembilan tabel inti lainnya karena tabel-tabel tersebut sudah dibuat oleh SQL import yang kamu lakukan. Ini disengaja agar backend tidak mengubah/menghapus schema tersebut.

Migration `database/migrations/2026_11_14_000001_create_personal_access_tokens_table.php` hanya membuat tabel token Sanctum. `php artisan migrate` boleh dijalankan untuk membuat tabel token dan migration tracking. Jangan publish migration Sanctum kedua kali dan jangan menambah migration yang membuat ulang tabel `users` atau tabel inti Barengin.

Jika `php artisan migrate:status` menunjukkan belum ada migration repository, itu normal sebelum migration token pertama kali dijalankan.

## 15. Batasan paket yang disediakan

Folder ini adalah source backend Laravel 13, bukan build/deployment server:
- `vendor/` tidak disertakan dan harus dibuat dengan `composer install`.
- `.env` dan APP_KEY milik laptop tidak disertakan.
- Belum ada akun demo atau fake seed user.
- Belum ada dashboard admin, notifikasi push/realtime chat, pembayaran, atau integrasi map provider.

Mulai dari health endpoint, lalu uji register/login dan alur Activity → Join Request → Host Approval sebelum menyambungkan seluruh layar Flutter.
