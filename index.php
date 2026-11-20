<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/web_helpers.php';
require __DIR__ . '/config/mock_data.php'; // Editorial company-profile copy only.
require __DIR__ . '/config/public_content.php';
require __DIR__ . '/includes/icons.php';
require __DIR__ . '/includes/actions.php';

process_web_action($barengin);

$allowedPages = ['home', 'detail', 'login', 'register', 'app', 'explore', 'activity', 'create', 'my-activities', 'favorites', 'profile', 'edit-profile', 'safety'];
$page = isset($_GET['page']) && in_array($_GET['page'], $allowedPages, true) ? $_GET['page'] : 'home';
$isApp = in_array($page, ['app', 'explore', 'activity', 'create', 'my-activities', 'favorites', 'profile', 'edit-profile', 'safety'], true);
if ($isApp) header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
$protectedPages = ['app', 'explore', 'activity', 'create', 'my-activities', 'favorites', 'profile', 'edit-profile', 'safety'];
if (in_array($page, $protectedPages, true)) require_auth_page($page);

$titleMap = [
    'home' => 'Temukan circle-mu',
    'detail' => 'Kenalan lebih dekat',
    'login' => 'Masuk ke Barengin',
    'register' => 'Gabung Barengin',
    'app' => 'Beranda',
    'explore' => 'Explore aktivitas',
    'activity' => 'Detail aktivitas',
    'create' => 'Buat aktivitas',
    'my-activities' => 'Aktivitas saya',
    'favorites' => 'Aktivitas tersimpan',
    'profile' => 'Profil',
    'edit-profile' => 'Edit profil',
    'safety' => 'Pusat keamanan',
];
$activityId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$currentActivity = null;
$currentActivityRaw = [];
$currentInfo = null;
foreach ($info_items as $item) {
    if ($item['id'] === ($_GET['id'] ?? 'find-your-circle')) {
        $currentInfo = $item;
        break;
    }
}
if (!$currentInfo) {
    $currentInfo = $info_items[0] ?? [];
}

$categories = [];
$activities = [];
$hostActivities = [];
$memberActivities = [];
$myJoinRequests = [];
$hostJoinRequests = [];
$favorites = [];
$profileData = current_user();
$blockedUsers = [];
$currentParticipants = [];
$apiError = null;
$apiAvailable = false;

if ($isApp) {
    try {
        // Validate the Sanctum token before rendering any private application page.
        $meResponse = $barengin->me();
        if (!empty(api_data($meResponse))) $_SESSION['barengin_auth']['user'] = api_data($meResponse);
        $health = $barengin->health();
        $apiAvailable = !empty($health['success']);
        $categoriesResponse = $barengin->categories();
        $categories = array_map('normalize_category', api_list($categoriesResponse));
        if ($page === 'app' || $page === 'explore') {
            $filters = ['per_page' => 24];
            if (!empty($_GET['q'])) $filters['q'] = trim((string) $_GET['q']);
            if (!empty($_GET['category'])) $filters['category_slug'] = (string) $_GET['category'];
            // The current profile city is the default Explore area; users can change it in Profile.
            $profileCity = trim((string) (current_user()['profile']['city'] ?? ''));
            if ($profileCity !== '') $filters['city'] = $profileCity;
            $activitiesResponse = $barengin->activities($filters);
            $activities = array_map('normalize_activity', api_list($activitiesResponse));
        }
        if ($page === 'activity') {
            if ($activityId < 1) throw new ApiException('ID aktivitas tidak valid.', 404);
            $response = $barengin->activity($activityId);
            $currentActivityRaw = api_data($response) ?? [];
            if ($currentActivityRaw) {
                $currentActivity = normalize_activity($currentActivityRaw);
                if ((int)($currentActivity['host_id'] ?? 0) === (int)(current_user()['id'] ?? 0)) {
                    $currentParticipants = api_list($barengin->participants($activityId));
                }
            }
        }
        if ($page === 'favorites') {
            $favorites = array_map('normalize_activity', api_list($barengin->favorites(['per_page' => 50])));
        }
        if ($page === 'my-activities') {
            $hostActivities = array_map('normalize_activity', api_list($barengin->myActivities(['role' => 'host', 'per_page' => 50])));
            $memberActivities = array_map('normalize_activity', api_list($barengin->myActivities(['role' => 'member', 'per_page' => 50])));
            $myJoinRequests = api_list($barengin->myJoinRequests(['per_page' => 50]));
            foreach ($hostActivities as $activity) {
                if (in_array($activity['status'], ['published', 'ongoing'], true)) {
                    try {
                        $requests = api_list($barengin->activityJoinRequests($activity['id'], ['per_page' => 50]));
                        foreach ($requests as $request) $hostJoinRequests[] = $request;
                    } catch (ApiException) { /* Some activities may not accept Host request management anymore. */
                    }
                }
            }
        }
        if (in_array($page, ['profile', 'edit-profile'], true)) {
            $profileData = api_data($barengin->profile()) ?? current_user();
            $_SESSION['barengin_auth']['user'] = $profileData;
        }
        if ($page === 'safety') $blockedUsers = api_list($barengin->blockedUsers());
    } catch (ApiException $exception) {
        $apiError = api_error_message($exception);
        if ($exception->statusCode === 401) {
            unset($_SESSION['barengin_auth']);
            session_regenerate_id(true);
            set_flash('warning', 'Sesi login sudah berakhir. Silakan masuk kembali.');
            redirect_page('login');
        }
    }
}

if ($page === 'activity' && !$currentActivity) {
    if ($apiError) set_flash('error', 'Detail aktivitas belum dapat dimuat: ' . $apiError);
    redirect_page('explore');
}

$activeNav = match ($page) {
    'explore' => 'explore',
    'create' => 'create',
    'my-activities' => 'activity',
    'favorites' => 'favorites',
    'profile', 'edit-profile', 'safety' => 'profile',
    default => 'home'
};
$flash = take_flash();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f7f1">
    <meta name="description" content="Barengin — temukan orang, mulai aktivitas, dan bikin cerita bareng.">
    <title><?= e($titleMap[$page] ?? 'Barengin') ?> · Barengin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="<?= $isApp ? 'app-body' : 'public-body' ?> page-<?= e($page) ?>">
    <?php if ($isApp): ?>
        <?php include __DIR__ . '/includes/app-shell-start.php'; ?>
        <?php if ($flash): ?><div class="container-fluid px-0">
                <div class="alert alert-<?= e($flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'warning' ? 'warning' : 'success')) ?> py-2 mb-3" role="status"><?= e($flash['message']) ?></div>
            </div><?php endif; ?>
        <?php if ($apiError): ?><div class="alert alert-warning mb-3" role="status"><strong>Laravel API belum bisa diakses.</strong> <?= e($apiError) ?> <span class="d-block small mt-1">Pastikan server Laravel berjalan di <?= e($config['base_url']) ?>.</span></div><?php endif; ?>
        <?php
        /** @var array<int, array<string, mixed>> $categories */
        /** @var array<string, mixed> $profileData */
        include __DIR__ . '/pages/' . $page . '.php';
        ?> <?php include __DIR__ . '/includes/app-shell-end.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/includes/public-header.php'; ?>
        <main>
            <?php if ($flash): ?><div class="container-xl pt-3">
                    <div class="alert alert-<?= e($flash['type'] === 'error' ? 'danger' : ($flash['type'] === 'warning' ? 'warning' : 'success')) ?>" role="status"><?= e($flash['message']) ?></div>
                </div><?php endif; ?>
            <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
        </main>
        <?php include __DIR__ . '/includes/public-footer.php'; ?>
    <?php endif; ?>
    <div class="toast-stack" id="toastStack" aria-live="polite"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>