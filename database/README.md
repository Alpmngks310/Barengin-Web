# Database Barengin

Database bersama untuk Project 1 (Barengin Website) dan Project 2 (CRUD API).

## Informasi Database

- Nama database: `barengin_db`
- DBMS: MySQL/MariaDB
- File SQL: `barengin_db.sql`

## Penggunaan

Database ini digunakan oleh dua project:

1. Project 1 — Laravel REST API mengakses database melalui Eloquent.
2. Project 2 — PHP Native CRUD mengakses tabel `activities` menggunakan PDO.

## Import Database

1. Jalankan MySQL melalui XAMPP atau Laragon.
2. Buka phpMyAdmin.
3. Buat database bernama `barengin_db`.
4. Pilih database tersebut.
5. Klik Import.
6. Pilih file `barengin_db.sql`.
7. Jalankan proses import.

## Keamanan

File SQL ini berisi struktur tabel database. Data pengguna, password, token autentikasi, dan informasi pribadi tidak disertakan.

Untuk penggunaan lokal, sesuaikan konfigurasi database pada Laravel `.env` dan koneksi PDO Project 2.
