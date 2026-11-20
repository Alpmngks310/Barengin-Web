<?php

/** @var array<int, array<string, mixed>> $categories */
/** @var array<int, array<string, mixed>> $activities */
?>

<section class="app-page-heading">
    <div><span class="app-kicker">FIND SOMETHING TO DO</span>
        <h1>Explore<span class="brand-period">.</span></h1>
        <p>Temukan hal seru dan orang baru di sekitarmu.</p>
    </div><a class="btn btn-soft" href="index.php?page=explore"><?= icon('refresh-cw') ?> Reset filter</a>
</section>
<section class="explore-tools">
    <form class="explore-search" method="get" action="index.php"><?= icon('search') ?><input type="hidden" name="page" value="explore"><input id="activitySearch" name="q" type="search" placeholder="Cari aktivitas atau tempat..." value="<?= e($_GET['q'] ?? '') ?>"><button type="submit" class="btn btn-sm btn-brand">Cari</button></form>
    <div class="explore-location-row"><span><?= icon('map-pin') ?> Area pencarian</span><span class="location-selector"><?= e(current_user()['profile']['city'] ?? 'Semua kota') ?> <?= icon('chevron-right') ?></span><span class="nearby-button"><?= icon('globe') ?> Hasil dari Laravel API</span></div>
    <div class="category-filter-row"><a class="filter-chip <?= empty($_GET['category']) ? 'active' : '' ?>" href="index.php?page=explore">Semua</a><?php foreach ($categories as $category): ?><a class="filter-chip <?= (($_GET['category'] ?? '') === $category['slug']) ? 'active' : '' ?>" href="index.php?page=explore&amp;category=<?= e($category['slug']) ?>"><?= e($category['name']) ?></a><?php endforeach; ?></div>
</section>
<div class="explore-results-header"><span id="resultsCount"><?= count($activities) ?> aktivitas ditemukan</span><span class="sort-button">Diambil langsung dari Barengin API</span></div>
<?php if ($activities): ?><div class="activity-card-grid explore-card-grid" id="exploreGrid"><?php foreach ($activities as $activity): $cardReturn = 'explore';
                                                                                                    include __DIR__ . '/../includes/activity-card.php';
                                                                                                endforeach; ?></div><?php else: ?><div class="empty-state" id="exploreEmpty">
        <div class="empty-state-icon"><?= icon('search') ?></div>
        <h3>Belum ada aktivitas yang cocok.</h3>
        <p>Coba kata kunci atau kategori lain, atau buat aktivitas baru.</p><a href="index.php?page=create" class="btn btn-brand">Buat aktivitas <?= icon('arrow-right') ?></a>
    </div><?php endif; ?>