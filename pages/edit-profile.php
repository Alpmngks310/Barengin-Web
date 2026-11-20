<?php $profile = $profileData['profile'] ?? []; ?>
<section class="app-page-heading">
    <div><span class="app-kicker">MAKE IT YOU</span>
        <h1>Edit profil<span class="brand-period">.</span></h1>
        <p>Perbarui detail yang ingin teman Barengin ketahui.</p>
    </div><a class="text-link" href="index.php?page=profile"><?= icon('arrow-left') ?> Kembali ke profil</a>
</section>
<form id="editProfileForm" class="form-panel edit-profile-form" method="post" enctype="multipart/form-data"><input type="hidden" name="_action" value="update_profile"><?= csrf_field() ?><input type="hidden" name="_return" value="edit-profile">
    <div class="edit-avatar-row">
        <div class="avatar avatar-profile"><?= e(current_user_initial()) ?></div>
        <div><strong>Foto profil</strong><span>JPG, PNG, atau WEBP. Maksimal 5 MB.</span></div><input class="form-control" type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
    </div>
    <div class="form-two-col">
        <div class="form-field"><label for="profileName">Nama lengkap</label><input id="profileName" name="name" maxlength="100" value="<?= e($profileData['name'] ?? '') ?>" required></div>
        <div class="form-field"><label for="profileDisplay">Nama tampilan</label><input id="profileDisplay" name="display_name" maxlength="80" value="<?= e($profile['display_name'] ?? $profileData['name'] ?? '') ?>" required></div>
    </div>
    <div class="form-two-col">
        <div class="form-field"><label for="profileUsername">Username</label><input id="profileUsername" name="username" maxlength="30" pattern="[A-Za-z0-9_-]{3,30}" value="<?= e($profileData['username'] ?? '') ?>"><small class="text-muted">3–30 karakter, huruf, angka, underscore, atau tanda hubung.</small></div>
        <div class="form-field"><label for="profileCity">Kota</label><input id="profileCity" name="city" maxlength="100" value="<?= e($profile['city'] ?? '') ?>"></div>
    </div>
    <div class="form-two-col">
        <div class="form-field"><label for="profileArea">Area</label><input id="profileArea" name="area" maxlength="120" value="<?= e($profile['area'] ?? '') ?>"></div>
        <div class="form-field"><label for="profileProvince">Provinsi</label><input id="profileProvince" name="province" maxlength="100" value="<?= e($profile['province'] ?? '') ?>"></div>
    </div>
    <div class="form-field"><label for="profileBio">Tentang saya</label><textarea id="profileBio" name="bio" rows="4" maxlength="500"><?= e($profile['bio'] ?? '') ?></textarea></div>
    <div class="form-submit-row"><span>Perubahan akan disimpan ke profil akun Barengin.</span><button type="submit" class="btn btn-brand">Simpan perubahan <?= icon('check') ?></button></div>
</form>