<?php
/** @var array<string, mixed> $currentInfo */
/** @var array<int, array<string, mixed>> $info_items */
?>

<section class="detail-hero container-xl">
    <a class="back-link" href="index.php"><?= icon('arrow-left') ?> Kembali ke Home</a>
    <div class="detail-hero-grid">
        <div><span class="section-kicker"><?= e($currentInfo['eyebrow']) ?></span><h1><?= e($currentInfo['title']) ?></h1><p class="detail-lead"><?= e($currentInfo['excerpt']) ?></p></div>
        <div class="detail-visual tone-<?= e($currentInfo['tone']) ?>"><div class="detail-visual-ring"></div><div class="detail-visual-icon"><?= icon($currentInfo['icon']) ?></div><span>GOOD THINGS<br>GROW TOGETHER ✳</span></div>
    </div>
</section>
<section class="detail-body container-xl">
    <div class="detail-body-copy"><span class="section-kicker">THE STORY</span><p><?= e($currentInfo['body']) ?></p><p>Di Barengin, kamu bisa mulai dari yang sederhana. Pilih minat, temukan aktivitas, dan biarkan pertemuan kecil membuka cerita yang baru.</p><a class="btn btn-brand" href="index.php?page=register">Coba Barengin <?= icon('arrow-up-right') ?></a></div>
    <aside class="detail-aside"><span class="aside-number">B / 2026</span><h3>Make space for<br>new connections.</h3><span class="aside-spark">✳</span><small>Find your people.<br>Make it happen.</small></aside>
</section>
<section class="detail-related container-xl"><div class="section-heading-row"><div><span class="section-kicker">KEEP EXPLORING</span><h2>More to <em>know.</em></h2></div></div><div class="related-grid">
<?php foreach ($info_items as $info): if ($info['id'] === $currentInfo['id']) continue; ?>
<a class="related-card" href="index.php?page=detail&id=<?= e($info['id']) ?>"><span><?= e($info['eyebrow']) ?></span><strong><?= e($info['title']) ?></strong><?= icon('arrow-up-right') ?></a>
<?php endforeach; ?>
</div></section>
