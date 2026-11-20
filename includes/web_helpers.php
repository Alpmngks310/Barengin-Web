<?php
declare(strict_types=1);

function e(mixed $value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function rupiah(int|float|string|null $amount): string { $value = (int) $amount; return $value <= 0 ? 'Gratis' : 'Rp' . number_format($value, 0, ',', '.'); }
function activity_visual(string $kind): string { return match ($kind) { 'film' => 'visual-film', 'game' => 'visual-game', 'run' => 'visual-run', 'study' => 'visual-study', default => 'visual-default' }; }
function category_tone(string $slug): string { return match ($slug) { 'sports-fitness' => 'mint', 'hangout-social' => 'peach', 'study-productivity' => 'butter', 'art-exploration' => 'blue', 'creative-hobby' => 'lilac', default => 'rose' }; }
function category_icon(string $slug): string { return match ($slug) { 'sports-fitness' => 'activity', 'hangout-social' => 'coffee', 'study-productivity' => 'book', 'art-exploration' => 'compass', 'creative-hobby' => 'palette', default => 'heart-handshake' }; }
function activity_visual_for_slug(string $slug): string { return match ($slug) { 'sports-fitness' => 'run', 'hangout-social' => 'film', 'study-productivity' => 'study', 'creative-hobby' => 'game', default => 'default' }; }
function api_data(array $response): mixed { return $response['data'] ?? null; }
function api_list(array $response): array { $data = api_data($response); return is_array($data) ? $data : []; }
function is_logged_in(): bool { return !empty($_SESSION['barengin_auth']['token']); }
function current_user(): array { return is_array($_SESSION['barengin_auth']['user'] ?? null) ? $_SESSION['barengin_auth']['user'] : []; }
function current_user_name(): string { $u = current_user(); return (string) (($u['profile']['display_name'] ?? '') ?: ($u['name'] ?? 'Teman Barengin')); }
function current_user_initial(): string { $name = current_user_name(); return function_exists('mb_substr') ? (mb_substr($name, 0, 1, 'UTF-8') ?: 'B') : (substr($name, 0, 1) ?: 'B'); }
function csrf_token_value(): string { if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32)); return $_SESSION['_csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token_value()) . '">'; }
function verify_csrf(): void { $sent = (string) ($_POST['_csrf'] ?? ''); if ($sent === '' || !hash_equals(csrf_token_value(), $sent)) { http_response_code(419); exit('Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.'); } }
function set_flash(string $type, string $message): void { $_SESSION['_flash'] = ['type' => $type, 'message' => $message]; }
function take_flash(): ?array { $flash = $_SESSION['_flash'] ?? null; unset($_SESSION['_flash']); return is_array($flash) ? $flash : null; }
function redirect_page(string $page = 'app', array $query = []): never { $allowed = ['home','detail','login','register','app','explore','activity','create','my-activities','favorites','profile','edit-profile','safety']; if (!in_array($page, $allowed, true)) $page = 'app'; $query = ['page' => $page] + $query; header('Location: index.php?' . http_build_query($query)); exit; }
function api_error_message(ApiException $exception): string {
    $errors = $exception->payload['errors'] ?? [];
    if (is_array($errors)) { foreach ($errors as $messages) { if (is_array($messages) && isset($messages[0])) return (string) $messages[0]; } }
    return $exception->getMessage();
}
function require_auth_page(string $page): void { if (!is_logged_in()) { set_flash('warning', 'Masuk terlebih dahulu untuk membuka halaman Barengin.'); redirect_page('login', ['next' => $page]); } }
function date_id(?string $iso): string {
    if (!$iso) return '-';
    try { $dt = new DateTimeImmutable($iso); } catch (Throwable) { return $iso; }
    $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $months = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return $days[(int)$dt->format('w')] . ', ' . $dt->format('d') . ' ' . $months[(int)$dt->format('n')] . ' ' . $dt->format('Y');
}
function time_range(?string $start, ?string $end): string {
    if (!$start) return '-';
    try { $a = new DateTimeImmutable($start); $b = $end ? new DateTimeImmutable($end) : null; } catch (Throwable) { return '-'; }
    return $a->format('H.i') . ($b ? '–' . $b->format('H.i') : '');
}
function normalize_activity(array $item): array {
    $category = $item['category'] ?? [];
    $venue = $item['venue'] ?? [];
    $budget = $item['budget'] ?? [];
    $host = $item['host'] ?? [];
    $slug = (string) ($category['slug'] ?? '');
    return [
        'id' => (int) ($item['id'] ?? 0),
        'title' => (string) ($item['title'] ?? 'Aktivitas Barengin'),
        'category' => (string) ($category['name'] ?? 'Aktivitas'),
        'category_slug' => $slug,
        'date' => date_id($item['starts_at'] ?? null),
        'time' => time_range($item['starts_at'] ?? null, $item['ends_at'] ?? null),
        'venue' => (string) ($venue['name'] ?? $venue['address'] ?? 'Lokasi belum diisi'),
        'address' => (string) ($venue['address'] ?? ''),
        'city' => (string) ($venue['city'] ?? ''),
        'area' => (string) ($venue['area'] ?? ''),
        'host' => (string) (($host['profile']['display_name'] ?? '') ?: ($host['name'] ?? 'Host Barengin')),
        'host_id' => (int) ($host['id'] ?? 0),
        'joined' => (int) ($item['joined_count'] ?? 0),
        'capacity' => (int) ($item['capacity'] ?? 1),
        'budget' => (int) ($budget['amount'] ?? 0),
        'level' => match ($item['participant_level'] ?? 'all_levels') { 'beginner' => 'Pemula', 'experienced' => 'Berpengalaman', default => 'Semua level' },
        'participant_level' => (string) ($item['participant_level'] ?? 'all_levels'),
        'description' => (string) ($item['description'] ?? ''),
        'status' => (string) ($item['status'] ?? 'published'),
        'visual' => activity_visual_for_slug($slug),
        'tag' => (string) ($item['status'] ?? 'Open for participants'),
        'is_favorited' => (bool) ($item['is_favorited'] ?? false),
        'starts_at' => (string) ($item['starts_at'] ?? ''),
        'ends_at' => (string) ($item['ends_at'] ?? ''),
        'available_slots' => (int) ($item['available_slots'] ?? 0),
        'raw' => $item,
    ];
}
function normalize_category(array $item): array {
    $slug = (string) ($item['slug'] ?? '');
    return ['id' => (int) ($item['id'] ?? 0), 'name' => (string) ($item['name'] ?? ''), 'slug' => $slug, 'description' => (string) ($item['description'] ?? ''), 'icon' => category_icon($slug), 'tone' => category_tone($slug)];
}
function status_label(string $status): string { return match ($status) { 'draft' => 'Draft', 'published' => 'Published', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'joined' => 'Joined', 'left' => 'Left', 'removed' => 'Removed', 'pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected', default => ucfirst($status) }; }
function status_class(string $status): string { return 'status-' . preg_replace('/[^a-z0-9_-]/', '', strtolower($status)); }
function current_page_url(): string { return 'index.php?' . http_build_query($_GET); }
