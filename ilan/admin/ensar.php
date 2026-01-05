<?php 
// 1. Hata Raporlama (Sorunu anlamak için aktif bıraktık)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); 
include '../includes/db.php'; 

// 2. Admin kontrolü
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// 3. --- OTOMATİK TABLO OLUŞTURUCU ---
// SQL ekranı açılmasa bile bu kod sayesinde destek mesajları tablon hazır olur.
$sql_tablo_kur = "CREATE TABLE IF NOT EXISTS destek_mesajlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isim VARCHAR(100),
    eposta VARCHAR(100),
    konu VARCHAR(200),
    mesaj TEXT,
    tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    durum INT DEFAULT 0
)";
$conn->query($sql_tablo_kur); 

// 4. Genel İstatistikler
$toplam_ilan_sorgu = $conn->query("SELECT COUNT(id) as sayi FROM ilanlar");
$toplam_ilan = $toplam_ilan_sorgu ? $toplam_ilan_sorgu->fetch_assoc()['sayi'] : 0;

$toplam_izlenme_sorgu = $conn->query("SELECT SUM(izlenme) as sayi FROM ilanlar");
$toplam_izlenme = ($toplam_izlenme_sorgu && $toplam_izlenme_sorgu->num_rows > 0) ? $toplam_izlenme_sorgu->fetch_assoc()['sayi'] : 0;

$toplam_yorum_sorgu = $conn->query("SELECT COUNT(id) as sayi FROM yorumlar");
$toplam_yorum = $toplam_yorum_sorgu ? $toplam_yorum_sorgu->fetch_assoc()['sayi'] : 0;

$toplam_mesaj_sorgu = $conn->query("SELECT COUNT(id) as sayi FROM destek_mesajlari");
$toplam_mesaj = $toplam_mesaj_sorgu ? $toplam_mesaj_sorgu->fetch_assoc()['sayi'] : 0;

// 5. İlan listesini çekelim
$sql = "SELECT ilanlar.*, kullanicilar.kullanici_adi, 
        (SELECT COUNT(id) FROM yorumlar WHERE ilan_id = ilanlar.id) as yorum_sayisi 
        FROM ilanlar 
        JOIN kullanicilar ON ilanlar.kullanici_id = kullanicilar.id 
        ORDER BY id DESC";
$ilanlar = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ensar Panel | Yönetim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        .sidebar { background: #1a1d20; min-height: 100vh; color: white; padding-top: 30px; position: fixed; width: 16.66667%; }
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 10px; border-radius: 12px; padding: 12px 20px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #ff4757; color: white; }
        .main-content { margin-left: 16.66667%; padding: 40px; }
        .stat-card { border: none; border-radius: 20px; padding: 25px; background: white; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        .main-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .btn-action { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; transition: 0.2s; }
        .btn-action:hover { transform: scale(1.1); background: #eee; }
        .img-custom { width: 50px; height: 50px; object-fit: cover; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar px-4">
            <h3 class="fw-bold mb-5 text-center text-danger">ENSAR<span class="text-white">.AD</span></h3>
            <ul class="nav flex-column">
                <li><a href="ensar.php" class="nav-link active"><i class="fas fa-home me-2"></i> Özet</a></li>
                <li><a href="mesajlar.php" class="nav-link"><i class="fas fa-envelope me-2"></i> Mesajlar <span class="badge bg-danger ms-1"><?= $toplam_mesaj ?></span></a></li>
                <li><a href="../index.php" class="nav-link"><i class="fas fa-eye me-2"></i> Siteyi Gör</a></li>
                <li><a href="../logout.php" class="nav-link text-warning mt-5"><i class="fas fa-power-off me-2"></i> Çıkış Yap</a></li>
            </ul>
        </div>

        <div class="col-md-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold text-dark m-0">İlan Yönetimi</h2>
                <div class="badge bg-white text-dark shadow-sm p-3 rounded-pill border">
                    <i class="fas fa-user-shield text-danger me-2"></i> Admin: <strong><?= isset($_SESSION['kullanici_adi']) ? $_SESSION['kullanici_adi'] : 'Admin' ?></strong>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold">İlanlar</small>
                        <h3 class="fw-bold m-0"><?= $toplam_ilan ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold">Ziyaret</small>
                        <h3 class="fw-bold m-0 text-danger"><?= number_format($toplam_izlenme) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold">Yorumlar</small>
                        <h3 class="fw-bold m-0 text-success"><?= $toplam_yorum ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card text-center border-primary border">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold text-primary">Destek</small>
                        <h3 class="fw-bold m-0 text-primary"><?= $toplam_mesaj ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="card main-card border-0 p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Resim / Başlık</th>
                                <th>Sahibi</th>
                                <th>Fiyat</th>
                                <th>Analiz</th>
                                <th class="text-end pe-3">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($ilanlar && $ilanlar->num_rows > 0): ?>
                                <?php while($row = $ilanlar->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <img src="../uploads/<?= $row['resim'] ?>" class="img-custom me-3 shadow-sm" onerror="this.src='https://placehold.jp/100x100.png'">
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($row['baslik']) ?></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark fw-normal border"><?= $row['kullanici_adi'] ?></span></td>
                                    <td class="text-danger fw-bold"><?= number_format($row['fiyat'], 0, ',', '.') ?> ₺</td>
                                    <td>
                                        <small class="d-block text-muted"><i class="fas fa-eye me-1"></i> <?= $row['izlenme'] ?></small>
                                        <small class="d-block text-muted"><i class="fas fa-comment me-1"></i> <?= $row['yorum_sayisi'] ?></small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="../view.php?id=<?= $row['id'] ?>" class="btn btn-action text-primary">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <a href="sil.php?id=<?= $row['id'] ?>" class="btn btn-action text-danger" onclick="return confirm('Silinsin mi?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center p-4 text-muted">Henüz ilan bulunmuyor.</td></tr>
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