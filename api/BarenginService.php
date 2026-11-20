<?php
declare(strict_types=1);

final class BarenginService
{
    public function __construct(private readonly ApiClient $client, private readonly ?string $token = null) {}

    public function health(): array { return $this->client->request('GET', '/health', [], null); }
    public function login(array $data): array { return $this->client->request('POST', '/auth/login', $data, null); }
    public function register(array $data): array { return $this->client->request('POST', '/auth/register', $data, null); }
    public function logout(): array { return $this->client->request('POST', '/auth/logout', [], $this->token); }
    public function me(): array { return $this->client->request('GET', '/me', [], $this->token); }
    public function categories(): array { return $this->client->request('GET', '/categories', [], $this->token); }
    public function activities(array $filters = []): array { return $this->client->request('GET', '/activities', $filters, $this->token); }
    public function activity(int $id): array { return $this->client->request('GET', '/activities/' . $id, [], $this->token); }
    public function createActivity(array $data): array { return $this->client->request('POST', '/activities', $data, $this->token); }
    public function updateActivity(int $id, array $data): array { return $this->client->request('PATCH', '/activities/' . $id, $data, $this->token); }
    public function publishActivity(int $id): array { return $this->client->request('POST', '/activities/' . $id . '/publish', [], $this->token); }
    public function cancelActivity(int $id, ?string $reason = null): array { return $this->client->request('POST', '/activities/' . $id . '/cancel', ['cancellation_reason' => $reason], $this->token); }
    public function leaveActivity(int $id): array { return $this->client->request('POST', '/activities/' . $id . '/leave', [], $this->token); }
    public function participants(int $id): array { return $this->client->request('GET', '/activities/' . $id . '/participants', [], $this->token); }
    public function removeParticipant(int $activityId, int $userId, ?string $note = null): array { return $this->client->request('DELETE', '/activities/' . $activityId . '/participants/' . $userId, ['removal_note' => $note], $this->token); }
    public function startActivity(int $id): array { return $this->client->request('POST', '/activities/' . $id . '/start', [], $this->token); }
    public function completeActivity(int $id): array { return $this->client->request('POST', '/activities/' . $id . '/complete', [], $this->token); }
    public function myActivities(array $filters = []): array { return $this->client->request('GET', '/my/activities', $filters, $this->token); }
    public function myJoinRequests(array $filters = []): array { return $this->client->request('GET', '/my/join-requests', $filters, $this->token); }
    public function cancelJoinRequest(int $id): array { return $this->client->request('DELETE', '/my/join-requests/' . $id, [], $this->token); }
    public function activityJoinRequests(int $id, array $filters = []): array { return $this->client->request('GET', '/activities/' . $id . '/join-requests', $filters, $this->token); }
    public function decideJoinRequest(int $id, array $data): array { return $this->client->request('PATCH', '/join-requests/' . $id . '/decision', $data, $this->token); }
    public function joinActivity(int $id, array $data): array { return $this->client->request('POST', '/activities/' . $id . '/join-requests', $data, $this->token); }
    public function favorites(array $filters = []): array { return $this->client->request('GET', '/my/favorites', $filters, $this->token); }
    public function favorite(int $id): array { return $this->client->request('POST', '/activities/' . $id . '/favorite', [], $this->token); }
    public function unfavorite(int $id): array { return $this->client->request('DELETE', '/activities/' . $id . '/favorite', [], $this->token); }
    public function profile(): array { return $this->client->request('GET', '/profile', [], $this->token); }
    public function updateProfile(array $data, bool $multipart = false): array
    {
        // POST + _method=PATCH keeps multipart upload compatible with PHP's request parser.
        if ($multipart) {
            $data['_method'] = 'PATCH';
            return $this->client->request('POST', '/profile', $data, $this->token, true);
        }
        return $this->client->request('PATCH', '/profile', $data, $this->token);
    }
    public function updateInterests(array $ids): array { return $this->client->request('PUT', '/profile/interests', ['category_ids' => $ids], $this->token); }
    public function report(array $data): array { return $this->client->request('POST', '/reports', $data, $this->token); }
    public function blockedUsers(): array { return $this->client->request('GET', '/my/blocked-users', [], $this->token); }
    public function blockUser(int $id): array { return $this->client->request('POST', '/users/' . $id . '/block', [], $this->token); }
    public function unblockUser(int $id): array { return $this->client->request('DELETE', '/users/' . $id . '/block', [], $this->token); }
}
