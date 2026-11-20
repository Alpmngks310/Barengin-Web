<section class="auth-page container-xl">
    <div class="auth-visual auth-visual-login"><a class="brand-lockup auth-brand" href="index.php"><span class="brand-mark"><span></span><span></span><span></span></span><span class="brand-word">barengin<span class="brand-period">.</span></span></a>
        <div class="auth-visual-copy"><span class="eyebrow-pill eyebrow-pill-light">YOUR NEXT GOOD STORY STARTS HERE</span>
            <h1>Hidup lebih<br>seru kalau<br><em>barengan.</em></h1>
            <p>Temukan circle baru, coba hal baru, dan bikin rencana jadi nyata.</p>
        </div>
        <div class="auth-visual-art">
            <div class="auth-orbit"></div>
            <div class="auth-people"><i></i><i></i><i></i></div><span class="auth-art-star">✳</span><span class="auth-art-note">meet / move / make</span>
        </div><span class="auth-visual-footer">© 2026 BARENGIN — MADE FOR BETTER TOGETHERNESS</span>
    </div>
    <div class="auth-form-side"><a class="auth-back" href="index.php"><?= icon('arrow-left') ?> Back to website</a>
        <div class="auth-form-wrap"><span class="section-kicker">WELCOME BACK!</span>
            <h2>Masuk ke<br>Barengin<span class="brand-period">.</span></h2>
            <p class="auth-subtitle">Lanjutkan cerita dan temukan rencana seru berikutnya.</p>
            <form id="loginForm" method="post" action="index.php"><input type="hidden" name="_action" value="login"><?= csrf_field() ?><input type="hidden" name="_return" value="login">
                <div class="form-field"><label for="loginEmail">Email</label>
                    <div class="input-with-icon"><?= icon('mail') ?><input id="loginEmail" name="email" type="email" placeholder="nama@email.com" autocomplete="email" maxlength="150" required></div>
                </div>
                <div class="form-field"><label for="loginPassword">Password</label>
                    <div class="input-with-icon"><?= icon('lock') ?><input id="loginPassword" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required><button type="button" class="input-trailing" data-toggle-password="loginPassword" aria-label="Tampilkan password"><?= icon('eye') ?></button></div>
                </div>
                <div class="form-inline-meta"><label class="check-label"><input type="checkbox" id="rememberMe" disabled> <span>Ingat saya (sesi browser ini)</span></label><span class="text-muted small"> </span></div>
                <button class="btn btn-brand btn-auth-submit" type="submit">Masuk ke Barengin <?= icon('arrow-right') ?></button>
            </form>
            <div class="auth-divider"><span></span> belum punya akun? <span></span></div><a class="btn btn-outline-brand btn-auth-outline" href="index.php?page=register">Buat akun baru</a>
            <div class="demo-credentials">
                <div class="demo-credentials-icon"><?= icon('shield-check') ?></div>
                <div><strong>Login terhubung ke akun Barengin</strong>
                    <p>Gunakan email dan password yang terdaftar pada database Barengin.</p><small>Website mengirim kredensial melalui Laravel API. Password tidak disimpan di website.</small>
                </div>
            </div>
        </div>
        <p class="auth-copyright">@Copyright by NPM_NAMA MAHASISWA_TIF RP 24 CID B</p>
    </div>
</section>