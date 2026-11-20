<?php

/** @var array<int, array<string, mixed>> $categories */
/** @var array<string, mixed> $profileData */
?>
<?php $profile = $profileData['profile'] ?? [];
$interests = $profileData['interests'] ?? [];
$interestIds = array_map(fn($item) => (int)($item['id'] ?? 0), $interests); ?>
<section class="app-page-heading">
    <div><span class="app-kicker">THIS IS YOUR SPACE</span>
        <h1>Profil<span class="brand-period">.</span></h1>
        <p>Kelola informasi dan preferensi akunmu.</p>
    </div><a class="icon-button profile-refresh" href="index.php?page=profile" aria-label="Muat ulang profil"><?= icon('refresh-cw') ?></a>
</section>
<div class="profile-layout">
    <section class="profile-main-card">
        <div class="profile-cover-art"><span>✳</span><small>YOUR SPACE, YOUR PACE</small></div>
        <div class="profile-identity">
            <div class="avatar avatar-profile"><?= e(current_user_initial()) ?></div>
            <div>
                <h2><?= e($profile['display_name'] ?? $profileData['name'] ?? 'Pengguna Barengin') ?></h2><span><?= e(!empty($profileData['username']) ? '@' . $profileData['username'] : ($profileData['email'] ?? '')) ?></span>
                <p><?= icon('map-pin') ?> <?= e($profile['area'] ?? '') ?><?= !empty($profile['area']) && !empty($profile['city']) ? ', ' : '' ?><?= e($profile['city'] ?? 'Lokasi belum diatur') ?></p>
            </div><a class="btn btn-outline-brand" href="index.php?page=edit-profile"><?= icon('edit') ?> Edit profil</a>
        </div>
        <div class="profile-bio"><span class="app-kicker">ABOUT ME</span>
            <p><?= e($profile['bio'] ?? 'Ceritakan sedikit tentang dirimu agar teman baru bisa mengenalmu.') ?></p>
        </div>
        <div class="profile-interest-block"><span class="app-kicker">MINAT KAMU</span>
            <div class="interest-chips"><?php if ($interests): foreach ($interests as $interest): ?><span><?= e($interest['name']) ?></span><?php endforeach;
                                                                                                                                    else: ?><span>Belum memilih minat</span><?php endif; ?></div>
        </div>
    </section>
    <aside class="profile-settings-card"><span class="app-kicker">PREFERENCES</span>
        <h3>Pengaturan & privasi</h3><a class="settings-row" href="index.php?page=edit-profile"><span class="settings-icon tone-mint"><?= icon('user') ?></span><span><strong>Edit data diri</strong><small>Nama, username, bio, dan lokasi</small></span><?= icon('arrow-up-right') ?></a><a class="settings-row" href="index.php?page=safety"><span class="settings-icon tone-butter"><?= icon('shield-check') ?></span><span><strong>Pusat keamanan</strong><small>Pengaturan blokir dan laporan</small></span><?= icon('arrow-up-right') ?></a>
        <form method="post" class="interest-form"><input type="hidden" name="_action" value="update_interests"><?= csrf_field() ?><input type="hidden" name="_return" value="profile"><strong>Perbarui minat (pilih 3–6)</strong>
            <div class="d-flex flex-column gap-2 mt-2"><?php foreach ($categories as $category): ?><label class="check-label"><input type="checkbox" name="category_ids[]" value="<?= (int)$category['id'] ?>" <?= in_array((int)$category['id'], $interestIds, true) ? 'checked' : '' ?>> <span><?= e($category['name']) ?></span></label><?php endforeach; ?></div><button class="btn btn-outline-brand btn-sm mt-3" type="submit">Simpan minat</button>
        </form>
        <form method="post"><input type="hidden" name="_action" value="logout"><?= csrf_field() ?><input type="hidden" name="_return" value="profile"><button class="settings-row settings-button" type="submit"><span class="settings-icon tone-rose"><?= icon('logout') ?></span><span><strong>Keluar dari akun</strong><small>Akhiri sesi Barengin di perangkat ini</small></span><?= icon('arrow-up-right') ?></button></form>
    </aside>
</div>