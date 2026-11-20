<header class="public-header">
    <nav class="public-nav container-xl">
        <a class="brand-lockup" href="index.php" aria-label="Barengin home">
            <span class="brand-mark"><span></span><span></span><span></span></span>
            <span class="brand-word">barengin<span class="brand-period">.</span></span>
        </a>
        <button class="nav-toggle" type="button" data-nav-toggle aria-label="Buka navigasi"><?= icon('menu') ?></button>
        <div class="public-nav-links" data-nav-menu>
            <a href="index.php#how-it-works">Cara kerja</a>
            <a href="index.php#categories">Kategori</a>
            <a href="index.php#about">Tentang kami</a>
            <div class="nav-mobile-actions">
                <?php if (is_logged_in()): ?><a class="btn btn-outline-brand" href="index.php?page=profile">Profil</a><a class="btn btn-brand" href="index.php?page=app">Buka Barengin <?= icon('arrow-up-right') ?></a><?php else: ?><a class="btn btn-outline-brand" href="index.php?page=login">Masuk</a><a class="btn btn-brand" href="index.php?page=register">Mulai bareng <?= icon('arrow-up-right') ?></a><?php endif; ?>
            </div>
        </div>
        <div class="public-nav-actions">
            <?php if (is_logged_in()): ?><a class="nav-login" href="index.php?page=profile">Profil</a><a class="btn btn-brand btn-nav-cta" href="index.php?page=app">Buka Barengin <?= icon('arrow-up-right') ?></a><?php else: ?><a class="nav-login" href="index.php?page=login">Masuk</a><a class="btn btn-brand btn-nav-cta" href="index.php?page=register">Mulai bareng <?= icon('arrow-up-right') ?></a><?php endif; ?>
        </div>
    </nav>
</header>
