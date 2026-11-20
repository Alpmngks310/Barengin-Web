<?php
/** @var array<int, array<string, mixed>> $blockedUsers */
?>

<section class="app-page-heading">
    <div><span class="app-kicker">COMMUNITY CARE</span>
        <h1>Pusat keamanan<span class="brand-period">.</span></h1>
        <p>Ruang yang nyaman dimulai dari sikap saling menjaga.</p>
    </div><a class="text-link" href="index.php?page=profile"><?= icon('arrow-left') ?> Kembali ke profil</a>
</section>
<div class="safety-hero">
    <div class="safety-hero-icon"><?= icon('shield-check') ?></div>
    <div><span class="app-kicker">YOUR SAFETY MATTERS</span>
        <h2>Kenalan boleh,<br>tetap <em>jaga batas.</em></h2>
        <p>Barengin ingin semua orang merasa aman, dihargai, dan punya kendali atas interaksi mereka.</p>
    </div>
</div>
<div class="safety-grid">
    <article class="safety-card"><span class="safety-icon tone-mint"><?= icon('users') ?></span>
        <h3>Akun yang diblokir</h3>
        <p>Kelola akun yang tidak ingin kamu temui atau ajak berinteraksi.</p><?php if ($blockedUsers): ?><div class="d-flex flex-column gap-2 mt-3"><?php foreach ($blockedUsers as $blocked): ?><div class="d-flex justify-content-between align-items-center"><span><?= e($blocked['name'] ?? $blocked['username'] ?? 'Pengguna') ?></span>
                        <form method="post"><input type="hidden" name="_action" value="unblock_user"><input type="hidden" name="user_id" value="<?= (int)$blocked['id'] ?>"><?= csrf_field() ?><input type="hidden" name="_return" value="safety"><button class="btn btn-sm btn-outline-secondary" type="submit">Buka blokir</button></form>
                    </div><?php endforeach; ?></div><?php else: ?><small class="text-muted">Belum ada akun yang diblokir.</small><?php endif; ?><form method="post" class="mt-3"><input type="hidden" name="_action" value="block_user"><?= csrf_field() ?><input type="hidden" name="_return" value="safety"><label class="form-label" for="blockUserId">ID pengguna yang ingin diblokir</label>
            <div class="input-group"><input class="form-control" type="number" min="1" name="user_id" id="blockUserId" required placeholder="User ID"><button class="btn btn-outline-brand" type="submit">Blokir</button></div>
        </form>
    </article>
    <article class="safety-card"><span class="safety-icon tone-peach"><?= icon('shield-check') ?></span>
        <h3>Laporkan perilaku</h3>
        <p>Laporkan akun atau aktivitas yang terasa tidak aman atau melanggar aturan.</p>
        <form method="post" class="mt-3"><input type="hidden" name="_action" value="report"><?= csrf_field() ?><input type="hidden" name="_return" value="safety">
            <div class="form-field"><label for="reportTarget">Jenis target</label><select class="form-select" id="reportTarget" name="target_type">
                    <option value="activity">Aktivitas</option>
                    <option value="user">Pengguna</option>
                </select></div>
            <div class="form-field"><label for="reportTargetId">ID target</label><input class="form-control" id="reportTargetId" data-report-target-id type="number" min="1" required></div>
            <div class="form-field"><label for="reportReason">Alasan</label><select class="form-select" id="reportReason" name="reason">
                    <option value="inappropriate_behavior">Perilaku tidak pantas</option>
                    <option value="misleading_activity">Aktivitas menyesatkan</option>
                    <option value="harassment_or_threat">Pelecehan atau ancaman</option>
                    <option value="spam_or_scam">Spam atau penipuan</option>
                    <option value="other">Lainnya</option>
                </select></div>
            <div class="form-field"><label for="reportDetails">Detail (opsional)</label><textarea class="form-control" id="reportDetails" name="details" rows="3" maxlength="1000"></textarea></div><button class="btn btn-brand" type="submit">Kirim laporan</button>
        </form>
    </article>
    <article class="safety-card"><span class="safety-icon tone-butter"><?= icon('lock') ?></span>
        <h3>Tips tetap aman</h3>
        <p>Jaga informasi pribadi, pilih lokasi pertemuan yang aman, dan beri tahu orang terdekat.</p>
        <ul>
            <li>Jangan membagikan password atau kode verifikasi.</li>
            <li>Periksa detail dan lokasi kegiatan sebelum bergabung.</li>
            <li>Gunakan fitur laporan jika menemukan perilaku berisiko.</li>
        </ul>
    </article>
</div>