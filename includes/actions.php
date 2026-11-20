<?php

declare(strict_types=1);

function process_web_action(BarenginService $api): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['_action'])) return;
    verify_csrf();
    $action = (string) $_POST['_action'];
    $back = (string) ($_POST['_return'] ?? 'app');
    try {
        switch ($action) {
            case 'login': {
                    $response = $api->login([
                        'email' => trim((string) ($_POST['email'] ?? '')),
                        'password' => (string) ($_POST['password'] ?? ''),
                        'device_name' => 'barengin-web',
                    ]);
                    $data = api_data($response) ?? [];
                    if (empty($data['token'])) throw new ApiException('Login API tidak mengembalikan token.');
                    session_regenerate_id(true);
                    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
                    $_SESSION['barengin_auth'] = ['token' => $data['token'], 'user' => $data['user'] ?? []];
                    set_flash('success', 'Login berhasil. Selamat datang di Barengin!');
                    redirect_page('app');
                }
            case 'register': {
                    $name = trim((string) ($_POST['name'] ?? ''));
                    $response = $api->register([
                        'name' => $name,
                        'username' => trim((string) ($_POST['username'] ?? '')) ?: null,
                        'email' => trim((string) ($_POST['email'] ?? '')),
                        'password' => (string) ($_POST['password'] ?? ''),
                        'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
                        'adult_confirmed' => !empty($_POST['adult_confirmed']) ? 1 : 0,
                        'terms_accepted' => !empty($_POST['terms_accepted']) ? 1 : 0,
                        'privacy_accepted' => !empty($_POST['privacy_accepted']) ? 1 : 0,
                        'device_name' => 'barengin-web',
                    ]);
                    $data = api_data($response) ?? [];
                    if (empty($data['token'])) throw new ApiException('Registrasi API tidak mengembalikan token.');
                    session_regenerate_id(true);
                    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
                    $_SESSION['barengin_auth'] = ['token' => $data['token'], 'user' => $data['user'] ?? []];
                    set_flash('success', 'Akun Barengin berhasil dibuat. Selamat bergabung!');
                    redirect_page('app');
                }
            case 'logout': {
                    $logoutType = 'success';
                    $logoutMessage = 'Kamu berhasil keluar dari Barengin.';
                    try {
                        $api->logout();
                    } catch (ApiException $exception) {
                        // Local session must still be cleared when the API is temporarily unavailable.
                        if ($exception->statusCode !== 401) {
                            $logoutType = 'warning';
                            $logoutMessage = 'Sesi website sudah ditutup, tetapi Laravel API tidak dapat dihubungi untuk mencabut token. Saat API kembali aktif, periksa sesi/token akun jika diperlukan.';
                        }
                    }
                    unset($_SESSION['barengin_auth']);
                    session_regenerate_id(true);
                    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
                    set_flash($logoutType, $logoutMessage);
                    redirect_page('home');
                }
            case 'favorite': {
                    require_auth_page('explore');
                    $id = max(0, (int) ($_POST['activity_id'] ?? 0));
                    if ($id < 1) throw new ApiException('Aktivitas tidak valid.', 422);
                    if (($_POST['favorite_mode'] ?? 'add') === 'remove') $api->unfavorite($id);
                    else $api->favorite($id);
                    set_flash('success', 'Daftar aktivitas tersimpan berhasil diperbarui.');
                    redirect_page($back, isset($_POST['activity_id']) && $back === 'activity' ? ['id' => $id] : []);
                }

            case 'join_activity': {
                    require_auth_page('activity');

                    $id = filter_var(
                        $_POST['activity_id'] ?? null,
                        FILTER_VALIDATE_INT
                    );

                    if ($id === false || $id === null || $id < 1) {
                        throw new ApiException(
                            'ID aktivitas tidak valid. Silakan buka kembali detail aktivitas.',
                            422
                        );
                    }

                    $api->joinActivity($id, [
                        'message' => trim((string) ($_POST['message'] ?? '')) ?: null
                    ]);

                    set_flash('success', 'Permintaan bergabung berhasil dikirim ke Host.');
                    redirect_page('activity', ['id' => $id]);
                }

            case 'cancel_join_request': {
                    require_auth_page('my-activities');
                    $api->cancelJoinRequest((int) ($_POST['request_id'] ?? 0));
                    set_flash('success', 'Permintaan bergabung berhasil dibatalkan.');
                    redirect_page('my-activities');
                }
            case 'create_activity': {
                    require_auth_page('create');
                    $date = trim((string) ($_POST['activity_date'] ?? ''));
                    $startTime = trim((string) ($_POST['start_time'] ?? ''));
                    $endTime = trim((string) ($_POST['end_time'] ?? ''));
                    $timezone = 'Asia/Jakarta';
                    try {
                        $start = new DateTimeImmutable($date . ' ' . $startTime, new DateTimeZone($timezone));
                        $end = new DateTimeImmutable($date . ' ' . $endTime, new DateTimeZone($timezone));
                        if ($end <= $start) $end = $end->modify('+1 day');
                    } catch (Throwable) {
                        throw new ApiException('Tanggal dan waktu aktivitas belum valid.', 422);
                    }
                    $payload = [
                        'category_id' => (int) ($_POST['category_id'] ?? 0),
                        'title' => trim((string) ($_POST['title'] ?? '')),
                        'description' => trim((string) ($_POST['description'] ?? '')),
                        'participant_level' => (string) ($_POST['participant_level'] ?? 'all_levels'),
                        'starts_at' => $start->format(DATE_ATOM),
                        'ends_at' => $end->format(DATE_ATOM),
                        'timezone' => $timezone,
                        'venue_name' => trim((string) ($_POST['venue_name'] ?? '')),
                        'address' => trim((string) ($_POST['address'] ?? '')),
                        'area' => trim((string) ($_POST['area'] ?? '')) ?: null,
                        'city' => trim((string) ($_POST['city'] ?? '')),
                        'province' => trim((string) ($_POST['province'] ?? 'Jawa Barat')) ?: null,
                        'country_code' => 'ID',
                        'capacity' => (int) ($_POST['capacity'] ?? 8),
                        'budget_amount' => (int) ($_POST['budget_amount'] ?? 0),
                        'currency' => 'IDR',
                    ];
                    $created = api_data($api->createActivity($payload)) ?? [];
                    $newId = (int) ($created['id'] ?? 0);
                    if ($newId < 1) throw new ApiException('Aktivitas berhasil dibuat tetapi ID tidak ditemukan.');
                    // Create API stores draft first. Publish it so it becomes visible in public Explore.
                    try {
                        $api->publishActivity($newId);
                        set_flash('success', 'Aktivitas berhasil dibuat dan dipublikasikan!');
                    } catch (ApiException $publishError) {
                        set_flash('warning', 'Aktivitas tersimpan sebagai draft. Publikasi belum berhasil: ' . api_error_message($publishError));
                    }
                    redirect_page('activity', ['id' => $newId]);
                }
            case 'update_profile': {
                    require_auth_page('profile');
                    $payload = [
                        'name' => trim((string) ($_POST['name'] ?? '')),
                        'username' => trim((string) ($_POST['username'] ?? '')),
                        'display_name' => trim((string) ($_POST['display_name'] ?? '')),
                        'bio' => trim((string) ($_POST['bio'] ?? '')),
                        'area' => trim((string) ($_POST['area'] ?? '')),
                        'city' => trim((string) ($_POST['city'] ?? '')),
                        'province' => trim((string) ($_POST['province'] ?? '')),
                        'country_code' => 'ID',
                    ];
                    $hasAvatar = isset($_FILES['avatar']) && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
                    if ($hasAvatar) $payload['avatar'] = $_FILES['avatar'];
                    $response = $api->updateProfile($payload, $hasAvatar);
                    if (isset($_SESSION['barengin_auth']['user'])) $_SESSION['barengin_auth']['user'] = api_data($response) ?? $_SESSION['barengin_auth']['user'];
                    set_flash('success', 'Profil berhasil diperbarui.');
                    redirect_page('profile');
                }
            case 'update_interests': {
                    require_auth_page('profile');
                    $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['category_ids'] ?? [])), static fn(int $id): bool => $id > 0)));
                    if (count($ids) < 3 || count($ids) > 6) {
                        throw new ApiException('Pilih minimal 3 dan maksimal 6 kategori minat.', 422);
                    }
                    $response = $api->updateInterests($ids);
                    if (isset($_SESSION['barengin_auth']['user'])) $_SESSION['barengin_auth']['user'] = api_data($response) ?? $_SESSION['barengin_auth']['user'];
                    set_flash('success', 'Minat kamu berhasil diperbarui.');
                    redirect_page('profile');
                }
            case 'start_activity': {
                    require_auth_page('activity');
                    $id = (int) ($_POST['activity_id'] ?? 0);
                    $api->startActivity($id);
                    set_flash('success', 'Aktivitas berhasil dimulai.');
                    redirect_page('activity', ['id' => $id]);
                }
            case 'complete_activity': {
                    require_auth_page('activity');
                    $id = (int) ($_POST['activity_id'] ?? 0);
                    $api->completeActivity($id);
                    set_flash('success', 'Aktivitas berhasil ditandai selesai.');
                    redirect_page('activity', ['id' => $id]);
                }
            case 'remove_participant': {
                    require_auth_page('activity');
                    $activityId = (int) ($_POST['activity_id'] ?? 0);
                    $api->removeParticipant($activityId, (int) ($_POST['user_id'] ?? 0), trim((string) ($_POST['removal_note'] ?? '')) ?: null);
                    set_flash('success', 'Peserta berhasil dikeluarkan dari aktivitas.');
                    redirect_page('activity', ['id' => $activityId]);
                }
            case 'publish_activity': {
                    require_auth_page('activity');
                    $id = (int) ($_POST['activity_id'] ?? 0);
                    $api->publishActivity($id);
                    set_flash('success', 'Aktivitas berhasil dipublikasikan.');
                    redirect_page('activity', ['id' => $id]);
                }
            case 'cancel_activity': {
                    require_auth_page('my-activities');
                    $api->cancelActivity((int) ($_POST['activity_id'] ?? 0), trim((string) ($_POST['reason'] ?? '')) ?: null);
                    set_flash('success', 'Aktivitas berhasil dibatalkan.');
                    redirect_page(in_array($back, ['activity', 'my-activities'], true) ? $back : 'my-activities', $back === 'activity' ? ['id' => (int)($_POST['activity_id'] ?? 0)] : []);
                }
            case 'leave_activity': {
                    require_auth_page('my-activities');
                    $api->leaveActivity((int) ($_POST['activity_id'] ?? 0));
                    set_flash('success', 'Kamu sudah keluar dari aktivitas.');
                    redirect_page('my-activities');
                }
            case 'decide_join_request': {
                    require_auth_page('my-activities');
                    $id = (int) ($_POST['request_id'] ?? 0);
                    $api->decideJoinRequest($id, ['status' => (string) ($_POST['decision'] ?? ''), 'decision_note' => trim((string) ($_POST['decision_note'] ?? '')) ?: null]);
                    set_flash('success', 'Keputusan permintaan berhasil disimpan.');
                    redirect_page('my-activities');
                }
            case 'report': {
                    require_auth_page('safety');
                    $payload = ['reason' => (string) ($_POST['reason'] ?? 'other'), 'details' => trim((string) ($_POST['details'] ?? '')) ?: null];
                    if (!empty($_POST['reported_user_id'])) $payload['reported_user_id'] = (int) $_POST['reported_user_id'];
                    if (!empty($_POST['activity_id'])) $payload['activity_id'] = (int) $_POST['activity_id'];
                    $api->report($payload);
                    set_flash('success', 'Laporan berhasil dikirim. Terima kasih sudah menjaga komunitas.');
                    redirect_page('safety');
                }
            case 'block_user': {
                    require_auth_page('safety');
                    $api->blockUser((int) ($_POST['user_id'] ?? 0));
                    set_flash('success', 'Pengguna berhasil diblokir.');
                    redirect_page('safety');
                }
            case 'unblock_user': {
                    require_auth_page('safety');
                    $api->unblockUser((int) ($_POST['user_id'] ?? 0));
                    set_flash('success', 'Blokir pengguna berhasil dibatalkan.');
                    redirect_page('safety');
                }
            default:
                throw new ApiException('Aksi tidak dikenali.', 400);
        }
    } catch (ApiException $exception) {
        if ($exception->statusCode === 401) {
            unset($_SESSION['barengin_auth']);
            session_regenerate_id(true);
        }

        $type = $exception->statusCode === 422 ? 'warning' : 'error';
        set_flash($type, api_error_message($exception));

        // Pertahankan ID aktivitas ketika terjadi error pada halaman detail.
        $params = [];

        if ($back === 'activity') {
            $activityId = (int) ($_POST['activity_id'] ?? 0);

            if ($activityId > 0) {
                $params['id'] = $activityId;
            }
        }

        redirect_page($back, $params);
    } catch (Throwable $exception) {
        set_flash(
            'error',
            'Terjadi kendala saat memproses permintaan. Periksa input dan koneksi API.'
        );

        $params = [];

        if ($back === 'activity') {
            $activityId = (int) ($_POST['activity_id'] ?? 0);

            if ($activityId > 0) {
                $params['id'] = $activityId;
            }
        }

        redirect_page($back, $params);
    }
}
