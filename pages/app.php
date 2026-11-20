<?php

/** @var array<int, array<string, mixed>> $categories */
/** @var array<int, array<string, mixed>> $activities */
?>

<section class="app-welcome-row">
    <div><span class="app-kicker"><?= e(strtoupper(date('l, d F Y'))) ?> · <?= e(current_user()['profile']['city'] ?? 'INDONESIA') ?></span>
        <h1>Hai, <?= e(current_user_name()) ?>! <span class="wave-emoji">✌️</span></h1>
        <p>Mau ngapain hari ini?</p>
    </div>
    <a class="btn btn-brand app-welcome-cta" href="index.php?page=create"><?= icon('plus') ?> Buat aktivitas</a>
</section>
<section class="app-search-feature">
    <form class="app-search" action="index.php" method="get"><input type="hidden" name="page" value="explore"><?= icon('search') ?><input type="search" name="q" placeholder="Cari aktivitas, tempat, atau keseruan..." value="<?= e($_GET['q'] ?? '') ?>"><button type="submit" aria-label="Cari"><?= icon('arrow-right') ?></button><span class="search-shortcut">⌘ K</span></form>
    <div class="app-location-note"><?= icon('map-pin') ?><span>Menjelajah di <strong><?= e(current_user()['profile']['city'] ?? 'Indonesia') ?></strong></span><a href="index.php?page=profile">Atur profil</a></div>
</section>
<section class="app-promo-banner">
    <div class="promo-copy"><span class="promo-eyebrow">YOUR NEXT GOOD STORY STARTS HERE</span>
        <h2>Satu minat.<br>Banyak kemungkinan.</h2>
        <p>Cari circle baru, coba hal baru, dan bikin rencana jadi nyata.</p><a href="index.php?page=create" class="promo-button"><?= icon('plus') ?> Buat aktivitas</a>
    </div>
    <div class="promo-art">
        <div class="promo-orbit"></div>
        <div class="promo-person promo-person-a"></div>
        <div class="promo-person promo-person-b"></div>
        <div class="promo-person promo-person-c"></div><span class="promo-art-star">✳</span><span class="promo-art-word">together<br>looks good<br>on you.</span>
    </div>
</section>
<section class="app-section-block">
    <div class="app-section-title">
        <div><span class="app-kicker">PICK YOUR INTEREST</span>
            <h2>Cari sesuai vibe kamu</h2>
        </div><a class="text-link" href="index.php?page=explore">Lihat semua <?= icon('arrow-right') ?></a>
    </div>
    <?php if ($categories): ?><div class="app-category-grid"><?php foreach ($categories as $category): ?><a class="app-category-card tone-<?= e($category['tone']) ?>" href="index.php?page=explore&amp;category=<?= e($category['slug']) ?>"><span><?= icon($category['icon']) ?></span><strong><?= e($category['name']) ?></strong></a><?php endforeach; ?></div><?php else: ?><p class="text-muted">Kategori belum tersedia. Pastikan Laravel API aktif.</p><?php endif; ?>
</section>
<section class="app-section-block app-activity-section">
    <div class="app-section-title">
        <div><span class="app-kicker">GOOD PLANS AHEAD</span>
            <h2>Aktivitas buat kamu</h2>
        </div><a class="text-link" href="index.php?page=explore">Lihat semua <?= icon('arrow-right') ?></a>
    </div>
    <?php if ($activities): ?><div class="activity-card-grid"><?php foreach (array_slice($activities, 0, 6) as $activity): $cardReturn = 'app';
                                                                    include __DIR__ . '/../includes/activity-card.php';
                                                                endforeach; ?></div><?php else: ?><div class="empty-state">
            <div class="empty-state-icon"><?= icon('compass') ?></div>
            <h3>Belum ada aktivitas yang tersedia.</h3>
            <p>Jadilah yang pertama membuat rencana seru di Barengin.</p><a href="index.php?page=create" class="btn btn-brand">Buat aktivitas <?= icon('arrow-right') ?></a>
        </div><?php endif; ?>
</section>
<section class="app-bottom-callout">
    <div class="callout-spark">✳</div>
    <div><strong>Punya rencana sendiri?</strong><span>Ajak orang lain untuk ikut jadi bagian dari ceritamu.</span></div><a class="btn btn-outline-brand" href="index.php?page=create">Buat aktivitas <?= icon('arrow-up-right') ?></a>
</section>