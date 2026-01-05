<?php 
include 'includes/db.php'; 
session_start();

// 1. GİRİŞ KONTROLÜ
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$uid = $_SESSION['user_id'];
$mesaj = "";
$mesaj_tur = "info";

// --- 2. ŞİFRE VE PROFİL GÜNCELLEME İŞLEMİ ---
if(isset($_POST['guncelle'])){
    $yeni_ad = $conn->real_escape_string($_POST['k_ad']);
    $eski_sifre = $_POST['eski_sifre'];
    $yeni_sifre = $_POST['yeni_sifre'];

    // Kullanıcının mevcut verilerini çek
    $user_query = $conn->query("SELECT sifre FROM kullanicilar WHERE id = $uid");
    $user_data = $user_query->fetch_assoc();

    // Sadece kullanıcı adını güncellemek istiyorsa
    if(empty($eski_sifre) && empty($yeni_sifre)){
        $conn->query("UPDATE kullanicilar SET kullanici_adi = '$yeni_ad' WHERE id = $uid");
        $mesaj = "Kullanıcı adı güncellendi.";
        $mesaj_tur = "success";
    } 
    // Şifre değiştirmek istiyorsa
    else {
        if($eski_sifre == $user_data['sifre']){ // Not: Veritabanında şifreler düz metinse bu çalışır
            if(!empty($yeni_sifre)){
                $conn->query("UPDATE kullanicilar SET kullanici_adi = '$yeni_ad', sifre = '$yeni_sifre' WHERE id = $uid");
                $mesaj = "Profil ve şifre başarıyla güncellendi!";
                $mesaj_tur = "success";
            } else {
                $mesaj = "Yeni şifre boş olamaz!";
                $mesaj_tur = "danger";
            }
        } else {
            $mesaj = "Mevcut şifreniz hatalı!";
            $mesaj_tur = "danger";
        }
    }
}

// 3. KULLANICI BİLGİLERİNİ VE İSTATİSTİKLERİ ÇEK
$user = $conn->query("SELECT * FROM kullanicilar WHERE id = $uid")->fetch_assoc();
$ilan_sayisi = $conn->query("SELECT id FROM ilanlar WHERE kullanici_id = $uid")->num_rows;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim | Ensar İlan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .profile-card { background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .nav-pills .nav-link { color: #666; border-radius: 10px; font-weight: 600; }
        .nav-pills .nav-link.active { background-color: #ff4757; color: white; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #eee; }
    </style>
</head>
<body>

<div class="container py-5">
    <?php if($mesaj != ""): ?>
        <div class="alert alert-<?= $mesaj_tur ?> alert-dismissible fade show rounded-4 shadow-sm mb-4">
            <?= $mesaj ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="profile-card p-4 text-center">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?= $user['kullanici_adi'] ?>&background=ff4757&color=fff&size=128" class="rounded-circle shadow">
                </div>
                <h4 class="fw-bold"><?= htmlspecialchars($user['kullanici_adi']) ?></h4>
                <p class="text-muted small"><?= $user['email'] ?></p>
                <hr>
                <div class="d-flex justify-content-around">
                    <div>
                        <h5 class="mb-0 fw-bold"><?= $ilan_sayisi ?></h5>
                        <small class="text-muted">İlan</small>
                    </div>
                </div>
                <div class="mt-4 d-grid gap-2">
                    <a href="index.php" class="btn btn-outline-dark rounded-pill">Ana Sayfaya Dön</a>
                    <a href="logout.php" class="btn btn-danger rounded-pill">Çıkış Yap</a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="profile-card p-4">
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item w-50">
                        <button class="nav-link active w-100" data-bs-toggle="pill" data-bs-target="#settings"><i class="fas fa-user-cog me-2"></i>Hesap Ayarları</button>
                    </li>
                    <li class="nav-item w-50">
                        <a href="ilanlarim.php" class="nav-link w-100 text-center"><i class="fas fa-th-list me-2"></i>İlanlarımı Yönet</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="settings">
                        <form method="POST">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Bilgileri Güncelle</h5>
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" name="k_ad" class="form-control" value="<?= htmlspecialchars($user['kullanici_adi']) ?>" required>
                            </div>
                            
                            <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2">Şifre Değiştir</h5>
                            <p class="text-muted small">Şifrenizi değiştirmek istemiyorsanız alanları boş bırakın.</p>
                            
                            <div class="mb-3">
                                <label class="form-label">Mevcut Şifre</label>
                                <input type="password" name="eski_sifre" class="form-control" placeholder="Şu anki şifreniz">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Yeni Şifre</label>
                                <input type="password" name="yeni_sifre" class="form-control" placeholder="Yeni şifreniz">
                            </div>

                            <button type="submit" name="guncelle" class="btn btn-primary w-100 rounded-pill py-2 mt-3 shadow-sm">
                                DEĞİŞİKLİKLERİ KAYDET
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>