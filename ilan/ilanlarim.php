<?php 
include 'includes/db.php'; 
session_start();

// 1. GİRİŞ KONTROLÜ
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$uid = $_SESSION['user_id'];

// 2. İLAN SİLME İŞLEMİ (Tablo adın: ilanlar)
if(isset($_GET['sil'])){
    $sil_id = (int)$_GET['sil'];
    
    // Güvenlik: Önce bu ilan kullanıcıya mı ait kontrol et
    $kontrol = $conn->query("SELECT id FROM ilanlar WHERE id = $sil_id AND kullanici_id = $uid");
    
    if($kontrol->num_rows > 0) {
        // İlanı sil
        $conn->query("DELETE FROM ilanlar WHERE id = $sil_id");
        // İlana ait favorileri temizle
        $conn->query("DELETE FROM favoriler WHERE ilan_id = $sil_id");
        header("Location: ilanlarim.php?durum=silindi");
        exit;
    }
}

// 3. İLANLARI ÇEK (Veritabanındaki 'ilanlar' tablosundan)
$ilanlar = $conn->query("SELECT * FROM ilanlar WHERE kullanici_id = $uid ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>İlanlarımı Yönet | Ensar İlan</title>
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .page-header { background: #1e2125; color: white; padding: 40px 0; border-radius: 0 0 40px 40px; margin-bottom: 30px; }
        .manage-card { border: none; border-radius: 20px; transition: 0.3s; background: white; overflow: hidden; }
        .manage-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .ilan-img { width: 100px; height: 75px; object-fit: cover; border-radius: 12px; }
        .stat-box { background: #f8f9fa; border-radius: 12px; padding: 10px; text-align: center; min-width: 80px; }
        .btn-action { border-radius: 10px; font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="page-header shadow-lg">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-0">İlan Yönetimi</h2>
            <p class="text-white-50 mb-0">Yayınladığın ilanları buradan güncelleyebilir veya silebilirsin.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="profile.php" class="btn btn-outline-light rounded-pill px-4">Profilim</a>
            <a href="index.php" class="btn btn-danger rounded-pill px-4"><i class="fas fa-home me-2"></i>Vitrin</a>
        </div>
    </div>
</div>

<div class="container">
    <?php if(isset($_GET['durum']) && $_GET['durum'] == 'silindi'): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>İlan başarıyla kaldırıldı.
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="manage-card shadow-sm">
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Görsel</th>
                                <th>İlan Başlığı</th>
                                <th>İzlenme</th>
                                <th>Fiyat</th>
                                <th class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($ilanlar->num_rows > 0): ?>
                                <?php while($i = $ilanlar->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="uploads/<?= $i['resim'] ?>" class="ilan-img shadow-sm" onerror="this.src='https://placehold.jp/100x75.png'">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($i['baslik']) ?></div>
                                        <small class="text-muted">İlan ID: #<?= $i['id'] ?></small>
                                    </td>
                                    <td>
                                        <div class="stat-box border">
                                            <div class="fw-bold text-primary"><i class="fas fa-eye me-1"></i><?= $i['izlenme'] ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success fs-6">
                                            <?= number_format($i['fiyat'], 0, ',', '.') ?> ₺
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group gap-2">
                                            <a href="view.php?id=<?= $i['id'] ?>" class="btn btn-light btn-action" title="Görüntüle"><i class="fas fa-eye"></i></a>
                                            <a href="edit_ads.php?id=<?= $i['id'] ?>" class="btn btn-primary btn-action" title="Düzenle"><i class="fas fa-edit"></i></a>
                                            <a href="ilanlarim.php?sil=<?= $i['id'] ?>" class="btn btn-danger btn-action" onclick="return confirm('Bu ilanı kalıcı olarak silmek istediğine emin misin?')" title="Sil"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">Henüz hiç ilan vermemişsin.</p>
                                        <a href="add_ads.php" class="btn btn-danger rounded-pill px-4">Hemen İlan Ver</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>