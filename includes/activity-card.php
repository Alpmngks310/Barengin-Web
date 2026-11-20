<?php
$cardReturn = $cardReturn ?? 'explore';
$activity = $activity ?? [];
$isSaved = !empty($activity['is_favorited']);
?>
<article class="activity-card" data-activity-card data-category="<?= e($activity['category_slug']) ?>" data-title="<?= e(strtolower($activity['title'])) ?>" data-city="<?= e(strtolower($activity['city'])) ?>">
    <a class="activity-card-visual <?= e(activity_visual($activity['visual'])) ?>" href="index.php?page=activity&amp;id=<?= (int)$activity['id'] ?>"><span class="visual-label"><?= e($activity['tag']) ?></span><span class="visual-mark">✳</span><span class="visual-card-icon"><?= icon('users') ?></span></a>
    <div class="activity-card-body">
        <div class="activity-meta-top"><span class="category-label"><?= e($activity['category']) ?></span>
            <form class="save-form" method="post" action="index.php"><input type="hidden" name="_action" value="favorite"><input type="hidden" name="_csrf" value="<?= e(csrf_token_value()) ?>"><input type="hidden" name="_return" value="<?= e($cardReturn) ?>"><input type="hidden" name="activity_id" value="<?= (int)$activity['id'] ?>"><input type="hidden" name="favorite_mode" value="<?= $isSaved ? 'remove' : 'add' ?>"><button class="save-button <?= $isSaved ? 'is-saved' : '' ?>" type="submit" aria-label="<?= $isSaved ? 'Hapus dari tersimpan' : 'Simpan aktivitas' ?>" aria-pressed="<?= $isSaved ? 'true' : 'false' ?>"><?= icon('bookmark') ?></button></form>
        </div>
        <a class="activity-card-title" href="index.php?page=activity&amp;id=<?= (int)$activity['id'] ?>"><?= e($activity['title']) ?></a>
        <div class="activity-facts"><span><?= icon('calendar') ?> <?= e($activity['date']) ?> · <?= e($activity['time']) ?></span><span><?= icon('map-pin') ?> <?= e($activity['venue']) ?> · <?= e($activity['city']) ?></span></div>
        <div class="activity-card-bottom"><span><?= icon('users') ?> <?= (int)$activity['joined'] ?>/<?= (int)$activity['capacity'] ?> peserta</span><span class="activity-budget"><?= e(rupiah((int)$activity['budget'])) ?></span></div>
        <div class="participant-progress"><span style="width:<?= min(100, (int)round($activity['joined'] / max(1,$activity['capacity']) * 100)) ?>%"></span></div>
        <a class="activity-card-link" href="index.php?page=activity&amp;id=<?= (int)$activity['id'] ?>">Lihat detail <?= icon('arrow-up-right') ?></a>
    </div>
</article>
