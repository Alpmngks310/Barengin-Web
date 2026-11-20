<?php
/** @var array<string, mixed> $currentActivity */
/** @var array<string, mixed> $currentActivityRaw */
/** @var array<int, array<string, mixed>> $currentParticipants */
?>

<?php $isHost = (int)($currentActivity['host_id'] ?? 0) === (int)(current_user()['id'] ?? 0);
$rawCategory = $currentActivityRaw['category']['name'] ?? $currentActivity['category']; ?>
<div class="activity-detail-page">
    <a class="back-link app-back-link" href="index.php?page=explore"><?= icon('arrow-left') ?> Kembali ke Explore</a>
    <div class="activity-detail-grid">
        <div class="activity-detail-main">
            <div class="activity-detail-cover <?= e(activity_visual($currentActivity['visual'])) ?>"><span class="visual-label"><?= e(status_label($currentActivity['status'])) ?></span><span class="visual-mark">✳</span><span class="visual-card-icon"><?= icon('users') ?></span></div>
            <div class="activity-detail-title-row">
                <div><span class="category-label"><?= e($rawCategory) ?></span>
                    <h1><?= e($currentActivity['title']) ?></h1>
                    <p class="host-line">Dibuat oleh <strong><?= e($currentActivity['host']) ?></strong> <span class="verified-dot"><?= icon('check') ?></span></p>
                </div>
                <form method="post" action="index.php"><input type="hidden" name="_action" value="favorite"><input type="hidden" name="_csrf" value="<?= e(csrf_token_value()) ?>"><input type="hidden" name="_return" value="activity"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>"><input type="hidden" name="favorite_mode" value="<?= $currentActivity['is_favorited'] ? 'remove' : 'add' ?>"><button class="save-button save-large <?= $currentActivity['is_favorited'] ? 'is-saved' : '' ?>" type="submit" aria-label="Simpan aktivitas"><?= icon('bookmark') ?></button></form>
            </div>
            <div class="detail-info-grid">
                <div><?= icon('calendar') ?><span><small>Tanggal & waktu</small><strong><?= e($currentActivity['date']) ?></strong><small><?= e($currentActivity['time']) ?></small></span></div>
                <div><?= icon('map-pin') ?><span><small>Lokasi</small><strong><?= e($currentActivity['venue']) ?></strong><small><?= e($currentActivity['address']) ?> · <?= e($currentActivity['city']) ?></small></span></div>
                <div><?= icon('users') ?><span><small>Peserta</small><strong><?= (int)$currentActivity['joined'] ?> dari <?= (int)$currentActivity['capacity'] ?> orang</strong><small>Termasuk Host</small></span></div>
                <div><?= icon('wallet') ?><span><small>Estimasi budget</small><strong><?= e(rupiah((int)$currentActivity['budget'])) ?></strong><small>Per orang</small></span></div>
            </div>
            <section class="detail-description"><span class="app-kicker">ABOUT THIS PLAN</span>
                <h2>Tentang kegiatan</h2>
                <p><?= nl2br(e($currentActivity['description'])) ?></p>
                <div class="host-note"><?= icon('shield-check') ?><span><strong>Keep it kind, keep it clear.</strong><small>Pastikan kamu membaca detail kegiatan dan berkomunikasi dengan Host sebelum bertemu.</small></span></div>
            </section>
            <?php if ($isHost): ?><section class="form-panel mt-4">
                    <h3>Kelola aktivitas</h3>
                    <p>Status saat ini: <strong><?= e(status_label($currentActivity['status'])) ?></strong></p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($currentActivity['status'] === 'draft'): ?><form method="post"><input type="hidden" name="_action" value="publish_activity"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="activity"><button class="btn btn-brand" type="submit">Publikasikan</button></form><?php endif; ?>
                        <?php if ($currentActivity['status'] === 'published'): ?><form method="post"><input type="hidden" name="_action" value="start_activity"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="activity"><button class="btn btn-outline-brand" type="submit">Mulai aktivitas</button></form><?php endif; ?>
                        <?php if ($currentActivity['status'] === 'ongoing'): ?><form method="post"><input type="hidden" name="_action" value="complete_activity"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="activity"><button class="btn btn-outline-brand" type="submit">Tandai selesai</button></form><?php endif; ?>
                        <?php if (in_array($currentActivity['status'], ['published', 'ongoing'], true)): ?><form method="post"><input type="hidden" name="_action" value="cancel_activity"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="activity"><button class="btn btn-outline-danger" type="submit">Batalkan aktivitas</button></form><?php endif; ?>
                    </div>
                    <?php if (!empty($currentParticipants)): ?><h4 class="mt-4">Peserta terdaftar</h4>
                        <div class="d-flex flex-column gap-2"><?php foreach ($currentParticipants as $participant): $person = $participant['user'] ?? []; ?><div class="d-flex flex-wrap justify-content-between align-items-center border rounded-3 p-3"><span><strong><?= e($person['profile']['display_name'] ?? $person['name'] ?? 'Pengguna') ?></strong><small class="d-block text-muted"><?= e($participant['role'] ?? 'member') ?> · <?= e($person['username'] ?? '') ?></small></span><?php if (($participant['role'] ?? '') !== 'host'): ?><form method="post" class="d-flex gap-2"><input type="hidden" name="_action" value="remove_participant"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>"><input type="hidden" name="user_id" value="<?= (int)$participant['user_id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="activity"><button class="btn btn-sm btn-outline-danger" type="submit">Keluarkan</button></form><?php endif; ?></div><?php endforeach; ?></div><?php endif; ?>
                </section><?php endif; ?>
        </div>
        <aside class="activity-join-card"><span class="join-card-status"><i></i> <?= e(status_label($currentActivity['status'])) ?></span>
            <h3><?= $isHost ? 'Ini aktivitasmu!' : 'Sounds like your kind of plan?' ?></h3>
            <p><?= $isHost ? 'Bagikan aktivitas ini dan kelola permintaan peserta.' : 'Kenalan dulu, yuk! Kirim pesan singkat ke Host sebelum mengajukan permintaan bergabung.' ?></p>
            <div class="join-capacity"><span>Slot peserta</span><strong><?= (int)$currentActivity['joined'] ?>/<?= (int)$currentActivity['capacity'] ?></strong></div>
            <div class="participant-progress"><span style="width:<?= min(100, (int)round($currentActivity['joined'] / max(1, $currentActivity['capacity']) * 100)) ?>%"></span></div>
            <?php if (!$isHost && $currentActivity['status'] === 'published' && $currentActivity['available_slots'] > 0): ?><form method="post" action="index.php"><input type="hidden" name="_action" value="join_activity"><input type="hidden" name="_csrf" value="<?= e(csrf_token_value()) ?>"><input type="hidden" name="_return" value="activity"><input type="hidden" name="activity_id" value="<?= (int)$currentActivity['id'] ?>">
                    <div class="form-field"><label for="joinMessage">Pesan untuk Host (opsional)</label><textarea id="joinMessage" name="message" rows="3" maxlength="500" placeholder="Halo! Aku tertarik ikut..."></textarea></div><button class="btn btn-brand w-100" type="submit">Ajukan untuk bergabung <?= icon('arrow-right') ?></button>
                </form>
            <?php elseif ($isHost): ?><span class="join-card-foot">Kamu adalah Host aktivitas ini.</span><?php else: ?><span class="join-card-foot">Aktivitas ini tidak menerima permintaan bergabung saat ini.</span><?php endif; ?>
            <small class="join-card-foot">Permintaanmu akan ditinjau oleh Host.</small>
            <div class="join-card-divider"></div><span class="app-kicker">GOOD TO KNOW</span>
            <div class="good-to-know-row"><?= icon('check') ?> Kamu bisa membatalkan permintaan yang masih pending.</div>
            <div class="good-to-know-row"><?= icon('check') ?> Jaga komunikasi tetap sopan dan jelas.</div>
        </aside>
    </div>
</div>