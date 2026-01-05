<?php 
// 1. OTURUM VE VERİTABANI BAĞLANTISI
session_start(); 
include 'includes/db.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. İLAN KONTROLÜ
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$ilan_id = (int)$_GET['id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// --- YENİ: İZLENME SAYISINI ARTIR ---
$conn->query("UPDATE ilanlar SET izlenme = izlenme + 1 WHERE id = $ilan_id");

// 3. İLAN BİLGİLERİNİ ÇEK
$ilan_sorgu = $conn->query("SELECT ilanlar.*, kategoriler.isim AS kategori_adi, kullanicilar.kullanici_adi 
                            FROM ilanlar 
                            LEFT JOIN kategoriler ON ilanlar.kategori_id = kategoriler.id 
                            LEFT JOIN kullanicilar ON ilanlar.kullanici_id = kullanicilar.id 
                            WHERE ilanlar.id = $ilan_id");

$ilan = $ilan_sorgu->fetch_assoc();

if(!$ilan){
    die("Hata: İlan bulunamadı.");
}

// --- DÜZELTİLEN SATIRLAR (Hata Buradaydı) ---
$favoride_mi = false;
if($user_id > 0){
    // Veritabanı resminde gördüğümüz gibi 'user_id' yerine 'kullanici_id' kullanıyoruz
    $fav_check = $conn->query("SELECT * FROM favoriler WHERE kullanici_id = $user_id AND ilan_id = $ilan_id");
    if($fav_check && $fav_check->num_rows > 0) {
        $favoride_mi = true;
    }
}

// 4. YORUM EKLEME İŞLEMİ
if(isset($_POST['yorum_yap']) && isset($_SESSION['user_id'])){
    $yorum = $conn->real_escape_string($_POST['yorum_metni']);
    $kadi = $_SESSION['kullanici_adi']; 
    
    $conn->query("INSERT INTO yorumlar (ilan_id, kullanici_adi, yorum_metni) VALUES ($ilan_id, '$kadi', '$yorum')");
    header("Location: view.php?id=$ilan_id");
    exit();
}

// 5. MEVCUT YORUMLARI ÇEK
$yorumlar = $conn->query("SELECT * FROM yorumlar WHERE ilan_id = $ilan_id ORDER BY tarih DESC");

// 6. BENZER İLANLARI ÇEK (Aynı kategoriden)
$kat_id = $ilan['kategori_id'];
$benzerler = $conn->query("SELECT * FROM ilanlar WHERE kategori_id = $kat_id AND id != $ilan_id LIMIT 4");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ilan['baslik']) ?> | Ensar İlan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .admin-bar { background: #111; color: white; padding: 10px 0; border-bottom: 3px solid #ff4757; }
        .ilan-card { border: none; border-radius: 25px; overflow: hidden; background: white; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .img-main { width: 100%; height: 500px; object-fit: cover; border-radius: 25px; cursor: zoom-in; }
        .price-tag { font-size: 2.2rem; color: #ff4757; font-weight: 800; }
        .stat-badge { background: #f8f9fa; padding: 8px 15px; border-radius: 12px; font-size: 0.9rem; color: #666; }
        .btn-modern { border-radius: 15px; font-weight: 700; padding: 12px; transition: 0.3s; }
        .btn-fav { border: 2px solid #ff4757; color: #ff4757; background: white; transition: 0.3s; }
        .btn-fav.active { background: #ff4757 !important; color: white !important; }
        .comment-box { border-radius: 15px; border: 1px solid #eee; background: #fff; margin-bottom: 15px; }
        .benzer-img { height: 150px; object-fit: cover; border-radius: 15px; }
    </style>
</head>
<body>

<?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
<div class="admin-bar sticky-top text-center">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="fas fa-user-shield text-danger me-2"></i> Yönetici Modu</span>
        <div>
            <a href="admin/ilan_sil.php?id=<?= $ilan_id ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Silinsin mi?')">İLANLI SİL</a>
            <a href="admin/ensar.php" class="btn btn-light btn-sm rounded-pill fw-bold ms-2">ADMİN PANELİ</a>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php" class="btn btn-light rounded-pill px-4 shadow-sm"><i class="fas fa-arrow-left me-2"></i>Geri Dön</a>
        <div class="stat-badge shadow-sm"><i class="fas fa-eye text-primary me-1"></i> <?= $ilan['izlenme'] ?> Görüntülenme</div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <img src="uploads/<?= $ilan['resim'] ?>" class="img-main shadow-lg border">
        </div>

        <div class="col-lg-5">
            <div class="ilan-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-danger rounded-pill px-3 py-2 fs-6"><?= $ilan['kategori_adi'] ?></span>
                    
                    <form action="favori_islem.php" method="POST">
                        <input type="hidden" name="ilan_id" value="<?= $ilan_id ?>">
                        <button type="submit" class="btn btn-fav rounded-circle <?= $favoride_mi ? 'active' : '' ?>" style="width: 50px; height: 50px;" title="Favoriye Ekle">
                            <i class="<?= $favoride_mi ? 'fas' : 'far' ?> fa-heart fa-lg"></i>
                        </button>
                    </form>
                </div>

                <h2 class="fw-bold text-dark mb-2"><?= htmlspecialchars($ilan['baslik']) ?></h2>
                <div class="price-tag mb-4"><?= number_format($ilan['fiyat'], 0, ',', '.') ?> ₺</div>
                
                <div class="bg-light p-4 rounded-4 mb-4 border">
                    <h6 class="fw-bold text-uppercase small text-muted mb-2">Açıklama</h6>
                    <p class="text-dark mb-0" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($ilan['aciklama'])) ?></p>
                </div>

                <div class="d-flex align-items-center p-3 border rounded-4 mb-4 bg-white">
                    <div class="profile-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ms-3">
                        <small class="text-muted d-block">İlan Sahibi</small>
                        <span class="fw-bold"><?= htmlspecialchars($ilan['kullanici_adi']) ?></span>
                    </div>
                </div>

                <div class="d-grid">
                    <a href="https://wa.me/905XXXXXXXXX?text=<?= urlencode($ilan['baslik']) ?> ilanıyla ilgileniyorum." class="btn btn-success btn-modern shadow">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp'tan Sor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if($benzerler->num_rows > 0): ?>
    <div class="mt-5">
        <h4 class="fw-bold mb-4">Bu Kategorideki Diğer İlanlar</h4>
        <div class="row g-3">
            <?php while($b = $benzerler->fetch_assoc()): ?>
            <div class="col-md-3">
                <a href="view.php?id=<?= $b['id'] ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <img src="uploads/<?= $b['resim'] ?>" class="benzer-img card-img-top">
                        <div class="card-body p-2 text-center">
                            <h6 class="text-dark text-truncate small mb-1"><?= $b['baslik'] ?></h6>
                            <span class="text-danger fw-bold small"><?= number_format($b['fiyat'], 0, ',', '.') ?> ₺</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="ilan-card p-4">
                <h4 class="fw-bold mb-4"><i class="far fa-comments me-2 text-danger"></i>Yorumlar (<?= $yorumlar->num_rows ?>)</h4>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="mb-5">
                        <textarea name="yorum_metni" class="form-control mb-3" rows="3" placeholder="Fiyat veya ürün hakkında bir şeyler sor..." required style="border-radius: 15px; border: 2px solid #eee;"></textarea>
                        <button type="submit" name="yorum_yap" class="btn btn-danger btn-modern px-5 shadow-sm">Gönder</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning rounded-4 border-0 p-3 text-center small">
                        Yorum yapabilmek için lütfen <a href="login.php" class="fw-bold">giriş yapın</a>.
                    </div>
                <?php endif; ?>

                <div class="comment-list">
                    <?php while($y = $yorumlar->fetch_assoc()): ?>
                        <div class="comment-box p-3 shadow-sm border-0">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold small text-danger fw-bold"><?= htmlspecialchars($y['kullanici_adi']) ?></span>
                                <small class="text-muted" style="font-size: 11px;"><?= date('d.m.Y H:i', strtotime($y['tarih'])) ?></small>
                            </div>
                            <p class="mb-0 small text-secondary"><?= htmlspecialchars($y['yorum_metni']) ?></p>
                            
                            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                                <hr class="my-2 opacity-25">
                                <a href="admin/yorum_sil.php?id=<?= $y['id'] ?>&ilan_id=<?= $ilan_id ?>" class="text-danger small text-decoration-none" onclick="return confirm('Silinsin mi?')">
                                    <i class="fas fa-trash-alt me-1"></i> Yönetici Olarak Sil
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>