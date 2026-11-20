<?php

/** @var array<int, array<string, mixed>> $hostActivities */
/** @var array<int, array<string, mixed>> $memberActivities */
/** @var array<int, array<string, mixed>> $myJoinRequests */
/** @var array<int, array<string, mixed>> $hostJoinRequests */
?>

<section class="app-page-heading">
    <div><span class="app-kicker">YOUR PLANS, YOUR PEOPLE</span>
        <h1>Aktivitas saya<span class="brand-period">.</span></h1>
        <p>Kelola kegiatan yang kamu buat dan yang kamu ikuti.</p>
    </div><a class="btn btn-brand" href="index.php?page=create"><?= icon('plus') ?> Buat aktivitas</a>
</section>
<div class="activity-tabs" role="tablist"><button class="activity-tab active" data-activity-tab="host">Host <span><?= count($hostActivities) ?></span></button><button class="activity-tab" data-activity-tab="member">Member <span><?= count($memberActivities) ?></span></button><button class="activity-tab" data-activity-tab="request">Request <span><?= count($myJoinRequests) ?></span></button></div>
<div class="activity-tab-panel active" data-activity-panel="host">
    <div class="activity-list">
        <?php if ($hostActivities): foreach ($hostActivities as $a): ?>
                <article class="my-activity-card">
                    <div class="my-activity-visual <?= e(activity_visual($a['visual'])) ?>"><span>✳</span></div>
                    <div class="my-activity-copy">
                        <div class="activity-meta-top"><span class="category-label"><?= e($a['category']) ?></span><span class="status-pill <?= e(status_class($a['status'])) ?>"><?= e(status_label($a['status'])) ?></span></div>
                        <h3><a href="index.php?page=activity&amp;id=<?= (int)$a['id'] ?>"><?= e($a['title']) ?></a></h3>
                        <p><?= icon('calendar') ?> <?= e($a['date']) ?> · <?= e($a['time']) ?></p>
                        <p><?= icon('map-pin') ?> <?= e($a['venue']) ?> · <?= e($a['city']) ?></p>
                        <div class="my-activity-footer"><span><?= icon('users') ?> <?= (int)$a['joined'] ?> peserta terdaftar</span><a href="index.php?page=activity&amp;id=<?= (int)$a['id'] ?>">Kelola aktivitas <?= icon('arrow-up-right') ?></a></div>
                    </div>
                </article>
            <?php endforeach;
        else: ?><div class="empty-state">
                <h3>Belum ada aktivitas yang kamu host.</h3>
                <p>Buat rencana pertama dan undang circle baru.</p><a class="btn btn-brand" href="index.php?page=create">Buat aktivitas</a>
            </div><?php endif; ?>
    </div>
</div>
<div class="activity-tab-panel" data-activity-panel="member">
    <div class="activity-list">
        <?php if ($memberActivities): foreach ($memberActivities as $a): ?><article class="my-activity-card">
                    <div class="my-activity-visual <?= e(activity_visual($a['visual'])) ?>"><span>✳</span></div>
                    <div class="my-activity-copy">
                        <div class="activity-meta-top"><span class="category-label"><?= e($a['category']) ?></span><span class="status-pill status-joined">Member</span></div>
                        <h3><a href="index.php?page=activity&amp;id=<?= (int)$a['id'] ?>"><?= e($a['title']) ?></a></h3>
                        <p><?= icon('calendar') ?> <?= e($a['date']) ?> · <?= e($a['time']) ?></p>
                        <p><?= icon('map-pin') ?> <?= e($a['venue']) ?> · <?= e($a['city']) ?></p>
                        <div class="my-activity-footer"><span><?= icon('users') ?> Kamu sudah terdaftar</span>
                            <form method="post"><input type="hidden" name="_action" value="leave_activity"><input type="hidden" name="activity_id" value="<?= (int)$a['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="my-activities"><button class="text-button" type="submit">Keluar dari aktivitas</button></form>
                        </div>
                    </div>
                </article><?php endforeach;
                    else: ?><div class="empty-state">
                <h3>Belum ada aktivitas yang kamu ikuti.</h3>
                <p>Temukan aktivitas baru di Explore.</p><a class="btn btn-brand" href="index.php?page=explore">Explore activities</a>
            </div><?php endif; ?>
    </div>
</div>
<div class="activity-tab-panel" data-activity-panel="request">
    <div class="activity-list">
        <?php if ($myJoinRequests): foreach ($myJoinRequests as $request): $requestActivity = $request['activity'] ?? [];
                $requestTitle = $requestActivity['title'] ?? 'Aktivitas'; ?>
                <article class="request-row">
                    <div class="request-avatar avatar avatar-peach"><?= e(substr((string)$requestTitle, 0, 1)) ?></div>
                    <div><strong><?= e($requestTitle) ?></strong><span><?= e(date_id($requestActivity['starts_at'] ?? null)) ?> · <?= e($requestActivity['city'] ?? '') ?></span></div><span class="status-pill <?= e(status_class($request['status'] ?? 'pending')) ?>"><?= e(status_label($request['status'] ?? 'pending')) ?></span><?php if (($request['status'] ?? '') === 'pending'): ?><form method="post"><input type="hidden" name="_action" value="cancel_join_request"><input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="my-activities"><button class="text-button" type="submit">Batalkan</button></form><?php endif; ?>
                </article>
            <?php endforeach;
        else: ?><div class="empty-state">
                <h3>Belum ada permintaan bergabung.</h3>
                <p>Ajukan permintaan pada aktivitas yang ingin kamu ikuti.</p><a class="btn btn-brand" href="index.php?page=explore">Cari aktivitas</a>
            </div><?php endif; ?>
    </div>
</div>
<?php if ($hostJoinRequests): ?><section class="app-section-block mt-4">
        <div class="app-section-title">
            <div><span class="app-kicker">HOST INBOX</span>
                <h2>Permintaan untuk aktivitasmu</h2>
            </div>
        </div>
        <div class="activity-list"><?php foreach ($hostJoinRequests as $request): if (($request['status'] ?? '') !== 'pending') continue;
                                        $rUser = $request['user'] ?? [];
                                        $rAct = $request['activity'] ?? []; ?><article class="request-row">
                    <div class="request-avatar avatar avatar-blue"><?= e(substr((string)($rUser['name'] ?? 'U'), 0, 1)) ?></div>
                    <div><strong><?= e($rUser['profile']['display_name'] ?? $rUser['name'] ?? 'Pengguna') ?></strong><span>Meminta bergabung ke <?= e($rAct['title'] ?? 'aktivitas') ?><?php if (!empty($request['message'])): ?> · “<?= e($request['message']) ?>”<?php endif; ?></span></div>
                    <form method="post"><input type="hidden" name="_action" value="decide_join_request"><input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>"><input type="hidden" name="decision" value="accepted"><?= csrf_field() ?><input type="hidden" name="_return" value="my-activities"><button class="btn btn-brand btn-sm" type="submit">Terima</button></form>
                    <form method="post"><input type="hidden" name="_action" value="decide_join_request"><input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>"><input type="hidden" name="decision" value="rejected"><?= csrf_field() ?><input type="hidden" name="_return" value="my-activities"><button class="btn btn-outline-secondary btn-sm" type="submit">Tolak</button></form>
                </article><?php endforeach; ?></div>
    </section><?php endif; ?>