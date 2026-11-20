<?php

/** @var string $activeNav */
?>

</main>
<footer class="app-footer"><span>© 2026 Barengin</span><span>Good things happen when we go together.</span><span>@Copyright by 24552011132_Adika Pamungkas_TIF RP 24B CID</span></footer>
</div>
<nav class="mobile-app-nav" aria-label="Navigasi aplikasi">
    <a class="<?= $activeNav === 'home' ? 'active' : '' ?>" href="index.php?page=app"><?= icon('home') ?><span>Home</span></a>
    <a class="<?= $activeNav === 'explore' ? 'active' : '' ?>" href="index.php?page=explore"><?= icon('compass') ?><span>Explore</span></a>
    <a class="mobile-create <?= $activeNav === 'create' ? 'active' : '' ?>" href="index.php?page=create"><span><?= icon('plus') ?></span><small>Create</small></a>
    <a class="<?= $activeNav === 'activity' ? 'active' : '' ?>" href="index.php?page=my-activities"><?= icon('calendar') ?><span>Activity</span></a>
    <a class="<?= $activeNav === 'profile' ? 'active' : '' ?>" href="index.php?page=profile"><?= icon('user') ?><span>Profile</span></a>
</nav>
</div>