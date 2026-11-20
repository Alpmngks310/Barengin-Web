# Backend Integration Map — Barengin Web

Dokumen ini mencatat hubungan halaman website dengan service PHP dan endpoint Laravel yang sudah ada pada backend mobile.

## Authentication dan session

| Website action | PHP handler | Laravel endpoint | Hasil |
|---|---|---|---|
| Login | `login` | `POST /api/auth/login` | Token Sanctum disimpan pada PHP session server-side |
| Register | `register` | `POST /api/auth/register` | Akun + profil dibuat oleh Laravel; token disimpan pada PHP session |
| Validasi session | `index.php` | `GET /api/me` | Data pengguna diperbarui untuk request halaman private |
| Logout | `logout` | `POST /api/auth/logout` | Token dicabut jika API tersedia, lalu session website dibersihkan |

## Data dan fitur pengguna

| Halaman / action | PHP service | Laravel endpoint |
|---|---|---|
| Home Dashboard | `activities`, `categories` | `GET /api/activities`, `GET /api/categories` |
| Explore + pencarian + kategori + kota profil | `activities` | `GET /api/activities?q=&category_slug=&city=&per_page=` |
| Detail aktivitas | `activity` | `GET /api/activities/{id}` |
| Simpan / hapus favorit | `favorite`, `unfavorite` | `POST/DELETE /api/activities/{id}/favorite` |
| Daftar favorit | `favorites` | `GET /api/my/favorites` |
| Minta bergabung | `joinActivity` | `POST /api/activities/{id}/join-requests` |
| Batalkan request sendiri | `cancelJoinRequest` | `DELETE /api/my/join-requests/{id}` |
| Aktivitas Host / Member | `myActivities` | `GET /api/my/activities?role=host|member` |
| Inbox permintaan Host | `activityJoinRequests`, `decideJoinRequest` | `GET /api/activities/{id}/join-requests`, `PATCH /api/join-requests/{id}/decision` |
| Edit profil | `profile`, `updateProfile` | `GET/PATCH /api/profile` |
| Minat akun | `updateInterests` | `PUT /api/profile/interests` |
| Daftar blokir | `blockedUsers` | `GET /api/my/blocked-users` |
| Block / unblock | `blockUser`, `unblockUser` | `POST/DELETE /api/users/{id}/block` |
| Laporkan pengguna / aktivitas | `report` | `POST /api/reports` |

## Create activity payload

Form Create Activity mengirim field yang tervalidasi oleh `StoreActivityRequest`:

- `category_id`
- `title`, `description`
- `participant_level`: `beginner`, `all_levels`, atau `experienced`
- `starts_at`, `ends_at`, `timezone`
- `venue_name`, `address`, `area`, `city`, `province`, `country_code`
- `capacity` (2–15, termasuk Host)
- `budget_amount`, `currency`

Laravel menyimpan aktivitas awal sebagai `draft` dan membuat baris peserta Host dalam transaksi yang sama. Setelah API mengembalikan ID aktivitas, website memanggil endpoint `publish`. Jika publikasi gagal, website memberi tahu bahwa aktivitas tetap tersimpan sebagai draft.

## Pemetaan SQL (tidak diakses langsung dari PHP)

- Identitas dan autentikasi: `users`, `personal_access_tokens`
- Profil dan minat: `user_profiles`, `user_interests`, `categories`
- Aktivitas dan peserta: `activities`, `activity_participants`
- Permintaan bergabung: `join_requests`
- Favorit: `activity_favorites`
- Fitur keamanan: `user_blocks`, `user_reports`

Tabel-tabel tersebut dikelola oleh Laravel. Website PHP tidak membuka koneksi MySQL dan tidak menjalankan query SQL langsung untuk fitur Barengin.

## Validasi / perilaku yang diselaraskan

- Registrasi mengirim konfirmasi usia 18+, terms, dan privacy; aturan final tetap diputuskan Laravel.
- Password hanya dikirim saat request login/register melalui POST HTTPS untuk deployment publik. Website tidak menyimpan password.
- Minat profil dibatasi 3–6 kategori aktif.
- Kapasitas aktivitas 2–15 orang termasuk Host.
- Waktu mulai harus di masa depan dan waktu selesai harus setelah waktu mulai.
- Join request dan transisi status aktivitas tetap tunduk pada policy/service Laravel.
- Report harus menargetkan tepat satu jenis: pengguna atau aktivitas.

## Hal yang tetap membutuhkan konfigurasi lokal

1. URL Laravel API di `config/api.php` atau `config/api.local.php`.
2. `.env` Laravel lokal dan koneksi database `barengin_db`.
3. Server Laravel harus berjalan ketika halaman aplikasi yang membutuhkan data dibuka.
4. Untuk avatar, PHP upload limit dan public storage link Laravel harus siap.
