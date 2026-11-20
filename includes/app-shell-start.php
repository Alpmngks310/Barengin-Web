<?php
/** @var string $activeNav */
/** @var array<string, string> $titleMap */
/** @var string $page */
?>

<div class="app-frame">
    <aside class="app-sidebar">
        <a class="brand-lockup sidebar-brand" href="index.php?page=app">
            <span class="brand-mark"><span></span><span></span><span></span></span>
            <span class="brand-word">barengin<span class="brand-period">.</span></span>
        </a>
        <div class="sidebar-caption">MENU UTAMA</div>
        <nav class="app-sidebar-nav" aria-label="Navigasi aplikasi">
            <a class="app-nav-link <?= $activeNav==='home'?'active':'' ?>" href="index.php?page=app"><?= icon('home') ?><span>Home</span></a>
            <a class="app-nav-link <?= $activeNav==='explore'?'active':'' ?>" href="index.php?page=explore"><?= icon('compass') ?><span>Explore</span><span class="nav-mini-dot"></span></a>
            <a class="app-nav-link <?= $activeNav==='create'?'active':'' ?>" href="index.php?page=create"><?= icon('plus-circle') ?><span>Create</span></a>
            <a class="app-nav-link <?= $activeNav==='activity'?'active':'' ?>" href="index.php?page=my-activities"><?= icon('calendar') ?><span>Activity</span></a>
            <a class="app-nav-link <?= $activeNav==='favorites'?'active':'' ?>" href="index.php?page=favorites"><?= icon('bookmark') ?><span>Saved</span></a>
        </nav>
        <div class="sidebar-caption sidebar-caption-space">PERSONAL</div>
        <nav class="app-sidebar-nav">
            <a class="app-nav-link <?= $activeNav==='profile'?'active':'' ?>" href="index.php?page=profile"><?= icon('user') ?><span>Profile</span></a>
        </nav>
        <div class="sidebar-bottom-card">
            <div class="sidebar-sparkle"><?= icon('sparkles') ?></div>
            <strong>Make room for new stories.</strong>
            <span>Mulai dari satu aktivitas kecil hari ini.</span>
            <a href="index.php?page=create">Buat aktivitas <?= icon('arrow-up-right') ?></a>
        </div>
        <div class="sidebar-user">
            <div class="avatar avatar-small"><?= e(current_user_initial()) ?></div>
            <div class="sidebar-user-copy"><strong><?= e(current_user_name()) ?></strong><span>Personal account</span></div>
            <form method="post" action="index.php"><input type="hidden" name="_action" value="logout"><?= csrf_field() ?><input type="hidden" name="_return" value="profile"><button class="icon-button" type="submit" aria-label="Keluar"><?= icon('logout') ?></button></form>
        </div>
    </aside>
    <div class="app-main-wrap">
        <header class="app-topbar">
            <div class="app-breadcrumb"><span>Workspace</span><?= icon('arrow-right') ?><strong><?= e($titleMap[$page] ?? 'Barengin') ?></strong></div>
            <div class="topbar-actions">
                <span class="location-pill"><?= icon('map-pin') ?> <?= e(current_user()['profile']['city'] ?? 'Indonesia') ?></span>
                <button class="icon-button topbar-notice" type="button" data-toast="Belum ada notifikasi baru." aria-label="Notifikasi"><?= icon('bell') ?><i></i></button>
                <a class="avatar avatar-small topbar-avatar" href="index.php?page=profile" aria-label="Profil"><?= e(current_user_initial()) ?></a>
            </div>
        </header>
        <main class="app-main-content">
