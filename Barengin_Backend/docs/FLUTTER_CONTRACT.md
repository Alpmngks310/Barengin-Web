# Flutter API Contract

Base URL:
- Android Emulator: `http://10.0.2.2:8000/api`
- Windows/Web: `http://127.0.0.1:8000/api`
- Physical Android: `http://<LAN-IP-LAPTOP>:8000/api`

Headers:
```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>
```

For multipart profile avatar upload use `Content-Type: multipart/form-data` and field `avatar`.

## Consistent response
Success:
```json
{ "success": true, "message": "OK", "data": {} }
```
Paginated:
```json
{
  "success": true,
  "message": "OK",
  "data": [],
  "meta": { "current_page": 1, "per_page": 12, "last_page": 1, "total": 0 }
}
```
Validation failure:
```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid.",
  "errors": { "field": ["Validation message"] }
}
```

Flutter should:
1. Save login token using secure storage.
2. Add `Authorization: Bearer ...` on private API calls.
3. Handle 401 by returning user to Login and clearing local token.
4. Handle 422 by showing field-level messages.
5. Handle 5xx/network failure with a retry state.
6. Send `starts_at` and `ends_at` as ISO-8601 timestamps including timezone offset.
7. Send GPS coordinates only when the user has granted location permission; do not persist them in profile.
8. Treat `join_requests.status` independently of `activities.status`.

## Optional authentication on public browse endpoints
`GET /activities` and `GET /activities/{id}` are public. If a Bearer token is supplied, the backend reads it through the Sanctum guard to personalize favorites and apply mutual block filtering; without a token, the endpoints still return public published activities.
