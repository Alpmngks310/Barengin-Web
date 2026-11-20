<?php
/** @var array<int, array<string, mixed>> $marketingCategories */
/** @var array<int, array<string, mixed>> $info_items */
?>

<section class="landing-hero container-xl">
    <div class="hero-copy">
        <div class="eyebrow-pill"><span class="eyebrow-dot"></span> YOUR NEXT GOOD STORY STARTS HERE</div>
        <h1>Jangan cuma<br><em>scroll.</em> Ketemu,<br>bikin cerita.</h1>
        <p class="hero-description">Punya minat yang sama? Ada rencana yang pengin diwujudkan? Temukan orang-orangnya dan mulai sesuatu bareng Barengin.</p>
        <div class="hero-actions">
            <a class="btn btn-brand btn-large" href="index.php?page=register">Cari circle kamu <?= icon('arrow-up-right') ?></a>
            <a class="hero-secondary-link" href="#how-it-works">Kenalan dulu <?= icon('arrow-right') ?></a>
        </div>
        <div class="hero-social-proof">
            <div class="avatar-stack"><span class="avatar avatar-pink">R</span><span class="avatar avatar-yellow">M</span><span class="avatar avatar-blue">N</span><span class="avatar avatar-dark">+</span></div>
            <div><strong>Mulai dari satu minat.</strong><span>Siapa tahu jadi circle baru.</span></div>
        </div>
    </div>
    <div class="hero-art" aria-label="Ilustrasi orang-orang yang berkumpul">
        <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
        <div class="hero-sticker sticker-top">GOOD VIBES<br>ONLY <span>✳</span></div>
        <div class="hero-sticker sticker-bottom"><span class="sticker-spark">✦</span> say yes to plans</div>
        <div class="hero-sun"></div>
        <div class="hero-person person-one"><span class="person-head"></span><span class="person-body"></span></div>
        <div class="hero-person person-two"><span class="person-head"></span><span class="person-body"></span></div>
        <div class="hero-person person-three"><span class="person-head"></span><span class="person-body"></span></div>
        <div class="hero-blob"></div>
        <div class="hero-art-note"><span>✳</span><div><strong>Plan less alone.</strong><small>Meet more, do more.</small></div></div>
    </div>
    <div class="hero-bottom-note"><span>FIND YOUR PEOPLE</span><span>SCROLL TO EXPLORE ↓</span></div>
</section>

<section class="marquee-strip" aria-label="Kategori aktivitas">
    <div class="marquee-track"><span>MEET</span><b>✳</b><span>MOVE</span><b>✳</b><span>MAKE</span><b>✳</b><span>LEARN</span><b>✳</b><span>EXPLORE</span><b>✳</b><span>MEET</span><b>✳</b><span>MOVE</span><b>✳</b><span>MAKE</span><b>✳</b><span>LEARN</span><b>✳</b></div>
</section>

<section class="section-block container-xl" id="categories">
    <div class="section-heading-row">
        <div><span class="section-kicker">FIND YOUR KIND OF FUN</span><h2>What's your <em>thing?</em></h2></div>
        <p>Mulai dari hal yang kamu suka.<br>Circle yang tepat bisa dimulai dari sana.</p>
    </div>
    <div class="category-showcase">
        <?php foreach ($marketingCategories as $index => $category): ?>
            <a class="category-tile tone-<?= e($category['tone']) ?>" href="index.php?page=<?= is_logged_in() ? 'explore' : 'login' ?>" data-reveal>
                <span class="category-tile-icon"><?= icon($category['icon']) ?></span>
                <span class="category-number">0<?= $index + 1 ?></span>
                <strong><?= e($category['name']) ?></strong>
                <small><?= e($category['description']) ?></small>
                <span class="category-tile-arrow"><?= icon('arrow-up-right') ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="how-section" id="how-it-works">
    <div class="container-xl how-grid">
        <div class="how-intro" data-reveal>
            <span class="section-kicker section-kicker-light">NO MORE "KAPAN-KAPAN"</span>
            <h2>From maybe<br>to <em>let's go.</em></h2>
            <p>Ide kecil nggak harus berhenti di chat. Barengin bikin langkah untuk bertemu, membuat rencana, dan melakukan hal yang kamu suka jadi lebih gampang.</p>
            <a class="btn btn-light-cream" href="index.php?page=register">Mulai sekarang <?= icon('arrow-up-right') ?></a>
            <div class="how-doodle">✳</div>
        </div>
        <div class="how-steps">
            <article class="how-step" data-reveal><span class="step-index">01</span><div class="step-icon step-icon-mint"><?= icon('compass') ?></div><div><h3>Find your vibe</h3><p>Jelajahi aktivitas sesuai minat dan area yang kamu pilih.</p></div><span class="step-arrow">↗</span></article>
            <article class="how-step" data-reveal><span class="step-index">02</span><div class="step-icon step-icon-peach"><?= icon('users') ?></div><div><h3>Meet your people</h3><p>Lihat detail rencana, kenalan dengan Host, lalu ajukan untuk bergabung.</p></div><span class="step-arrow">↗</span></article>
            <article class="how-step" data-reveal><span class="step-index">03</span><div class="step-icon step-icon-butter"><?= icon('sparkles') ?></div><div><h3>Make it happen</h3><p>Atau buat aktivitas sendiri dan ajak circle baru ikut seru-seruan.</p></div><span class="step-arrow">↗</span></article>
        </div>
    </div>
</section>

<section class="section-block container-xl story-section" id="about">
    <div class="section-heading-row">
        <div><span class="section-kicker">A LITTLE MORE ABOUT US</span><h2>Small plans.<br><em>Big stories.</em></h2></div>
        <a class="text-link" href="index.php?page=detail&id=find-your-circle">Kenali Barengin <?= icon('arrow-up-right') ?></a>
    </div>
    <div class="story-grid">
        <?php foreach ($info_items as $i => $info): ?>
            <a class="story-card tone-<?= e($info['tone']) ?>" href="index.php?page=detail&id=<?= e($info['id']) ?>" data-reveal>
                <span class="story-card-top"><span><?= e($info['eyebrow']) ?></span><?= icon('arrow-up-right') ?></span>
                <div class="story-card-symbol"><?= icon($info['icon']) ?></div>
                <h3><?= e($info['title']) ?></h3>
                <p><?= e($info['excerpt']) ?></p>
                <span class="story-card-cta">Read the story <?= icon('arrow-right') ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="closing-cta container-xl">
    <div class="closing-cta-inner" data-reveal>
        <div class="closing-stamp">YOUR<br>PEOPLE<br>ARE HERE <span>✳</span></div>
        <div><span class="section-kicker section-kicker-light">THE FIRST STEP IS YOURS</span><h2>Good things happen<br>when we go <em>together.</em></h2><p>Siap bikin rencana yang nggak cuma jadi wacana?</p><a class="btn btn-light-cream" href="index.php?page=register">Let's Barengin <?= icon('arrow-up-right') ?></a></div>
        <div class="closing-doodle">✳</div>
    </div>
</section>
