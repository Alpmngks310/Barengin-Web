# Frontend and backend integration notes

Frontend remains PHP-rendered and keeps the original Barengin web redesign. The user pages now use server-side PHP requests to the Laravel API.

- Company-profile content remains local editorial content in `config/mock_data.php` (`$info_items`).
- Categories and activity data are requested from Laravel API; mock activity arrays are no longer used for signed-in app pages.
- Login/register use normal POST forms and Laravel API authentication.
- Favourite and join-request actions submit through PHP handlers; no token is exposed to browser JavaScript.
- The app area requires a valid PHP session backed by a Laravel Sanctum token.
- Search/category filters are sent to the API rather than only filtering browser mock data.
