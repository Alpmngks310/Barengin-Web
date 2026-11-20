# Database Mapping (review against barengin_db v1.0)

This backend intentionally maps to the imported schema; it does not define migrations for the 10 core tables.

| Table | Model | Key detail |
|---|---|---|
| users | User | Laravel auth model, SoftDeletes, Sanctum HasApiTokens |
| user_profiles | UserProfile | primary key `user_id`, non-incrementing |
| categories | Category | seeded six categories |
| user_interests | User ↔ Category pivot | composite key `(user_id, category_id)` |
| activities | Activity | FK `host_id`, category_id; status enum |
| activity_favorites | User ↔ Activity pivot | composite key `(user_id, activity_id)` |
| join_requests | JoinRequest | unique activity-user pair; reviewer nullable |
| activity_participants | ActivityParticipant | unique activity-user pair; includes Host |
| user_blocks | User blockedUsers/blockedByUsers | directional pair |
| user_reports | UserReport | reported user and/or activity target |

## Important columns honored by code
- `users.adult_confirmed_at`, `terms_accepted_at`, `privacy_accepted_at` are populated on registration.
- `user_profiles.display_name` is always created at registration.
- Host participant record is inserted in the same transaction as new Activity.
- `activities.capacity` includes the Host.
- `activities.status` and `join_requests.status` remain separate.
- `user_interests` has only `created_at`, not `updated_at`; code does not request pivot timestamps.
- `activity_favorites` has only `created_at`; code does not request pivot timestamps.
- report foreign keys use RESTRICT; reports preserve references. Account deactivation/soft deletion is preferred to hard deletion.

## Sanctum
Sanctum stores API tokens in `personal_access_tokens`. This project includes a single migration for that table; `php artisan migrate` creates it plus Laravel's migration tracking table. No application core table is re-created.

## Lifecycle implementation notes
- Host can transition an activity `published -> ongoing` with the start endpoint when current time is inside `starts_at..ends_at`.
- Host can transition an `ongoing -> completed` activity with the complete endpoint.
- Host can remove a joined Member by setting participant status to `removed`; the Host participant row cannot be removed through this endpoint.

- A Member can cancel their own `pending` join request; that pending row is deleted.
- A Member can leave an accepted activity, setting participant status to `left`. If they request to join again, the unique existing request is reset to `pending` for a fresh Host decision.

- Activity edits and lifecycle transitions use row locks; cancelling/starting/completing an activity rejects any remaining pending join requests with a recorded Host decision.

- Member leave, Host participant removal, and pending request cancellation lock the Activity row before changing participant/request records. Approval and submission use the same Activity → JoinRequest/Participant lock order.
