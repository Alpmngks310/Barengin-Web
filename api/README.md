# PHP API integration layer

Backend website ini adalah PHP server-side client untuk Laravel REST API Barengin.

- `ApiClient.php`: HTTP transport (cURL jika aktif, PHP streams sebagai fallback), JSON/multipart, Bearer header, timeout, error mapping.
- `BarenginService.php`: wrapper endpoint sesuai `routes/api.php` dan `docs/ENDPOINTS.md` backend mobile.
- `../includes/actions.php`: memproses form POST setelah validasi CSRF dan memanggil service yang sesuai.

Token Sanctum berada pada `$_SESSION['barengin_auth']['token']`. Jangan memindahkannya ke localStorage atau HTML. Jangan membuka koneksi database dari website untuk fitur inti; Laravel yang memiliki validasi, authorization, transaksi, dan pengelolaan database.
