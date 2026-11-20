<?php
/** @var array<int, array<string, mixed>> $favorites */
?>

<section class="app-page-heading">
    <div><span class="app-kicker">YOUR LITTLE WISHLIST</span>
        <h1>Saved plans<span class="brand-period">.</span></h1>
        <p>Aktivitas yang menarik perhatianmu, tersimpan di satu tempat.</p>
    </div><a class="btn btn-outline-brand" href="index.php?page=explore">Explore activities <?= icon('arrow-up-right') ?></a>
</section>
<div class="saved-intro">
    <div class="saved-intro-icon"><?= icon('bookmark') ?></div>
    <div><strong>Keep the good plans close.</strong><span>Daftar ini tersinkron dengan akun Barengin kamu.</span></div>
</div>
<?php if ($favorites): ?><div class="activity-card-grid" id="savedGrid"><?php foreach ($favorites as $activity): $cardReturn = 'favorites';
                                                                            include __DIR__ . '/../includes/activity-card.php';
                                                                        endforeach; ?></div><?php else: ?><div class="empty-state" id="savedEmpty">
        <div class="empty-state-icon"><?= icon('bookmark') ?></div>
        <h3>Belum ada yang disimpan.</h3>
        <p>Masih banyak aktivitas seru yang bisa kamu temukan.</p><a href="index.php?page=explore" class="btn btn-brand">Cari aktivitas <?= icon('arrow-right') ?></a>
    </div><?php endif; ?>