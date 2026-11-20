# Barengin API Endpoints

Prefix: `/api`

| Method | Path | Auth | Request / Query |
|---|---|---:|---|
| GET | `/health` | No | — |
| POST | `/auth/register` | No | name, username?, email, password, password_confirmation, adult_confirmed, terms_accepted, privacy_accepted, device_name? |
| POST | `/auth/login` | No | email, password, device_name? |
| POST | `/auth/logout` | Yes | — |
| GET | `/me` | Yes | — |
| GET | `/categories` | No | — |
| GET | `/profile` | Yes | — |
| PATCH | `/profile` | Yes | name?, username?, display_name?, bio?, area?, city?, province?, country_code?, avatar? |
| PUT | `/profile/interests` | Yes | category_ids[] (3–6) |
| GET | `/activities` | Optional | q, category_id, category_slug, city, area, date_from, date_to, budget_max, participant_level, latitude, longitude, radius_km, per_page |
| GET | `/activities/{id}` | Optional | — |
| POST | `/activities` | Yes | Activity fields |
| PATCH | `/activities/{id}` | Yes | Partial Activity fields |
| POST | `/activities/{id}/publish` | Yes | — |
| POST | `/activities/{id}/cancel` | Yes | cancellation_reason? |
| POST | `/activities/{id}/leave` | Yes, Member | — |
| POST | `/activities/{id}/start` | Yes, Host | — |
| GET | `/activities/{id}/participants` | Yes, Host | — |
| DELETE | `/activities/{id}/participants/{userId}` | Yes, Host | removal_note? |
| POST | `/activities/{id}/complete` | Yes | — |
| POST | `/activities/{id}/join-requests` | Yes | message? |
| GET | `/activities/{id}/join-requests` | Yes, Host | per_page? |
| PATCH | `/join-requests/{id}/decision` | Yes, Host | status=accepted/rejected, decision_note? |
| GET | `/my/activities` | Yes | role=host/member/all, status?, per_page? |
| GET | `/my/join-requests` | Yes | per_page? |
| DELETE | `/my/join-requests/{id}` | Yes, pemilik request | hanya status pending |
| GET | `/my/favorites` | Yes | per_page? |
| POST | `/activities/{id}/favorite` | Yes | — |
| DELETE | `/activities/{id}/favorite` | Yes | — |
| POST | `/reports` | Yes | reported_user_id? or activity_id?, reason, details? |
| GET | `/my/blocked-users` | Yes | — |
| POST | `/users/{id}/block` | Yes | — |
| DELETE | `/users/{id}/block` | Yes | — |
