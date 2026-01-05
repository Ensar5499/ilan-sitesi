<?php 
include 'includes/db.php'; 
session_start();

// 1. Giriş kontrolü
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$uid = $_SESSION['user_id'];

// 2. Kullanıcının favorilediği ilanları kategori adıyla birlikte çekiyoruz
$fav_sorgu = "SELECT ilanlar.*, kategoriler.isim AS kategori_adi FROM favoriler 
              JOIN ilanlar ON favoriler.ilan_id = ilanlar.id 
              LEFT JOIN kategoriler ON ilanlar.kategori_id = kategoriler.id
              WHERE favoriler.kullanici_id = $uid 
              ORDER BY favoriler.id DESC";
$favoriler = $conn->query($fav_sorgu);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorilerim | Ensar İlan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        
        .page-header { background: #1e2125; color: white; padding: 50px 0; border-radius: 0 0 50px 50px; margin-bottom: 40px; }
        
        .card-fav { 
            border: none; 
            border-radius: 25px; 
            transition: 0.3s; 
            background: white; 
            overflow: hidden; 
            height: 100%;
        }
        .card-fav:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.1); 
        }
        
        .img-container { position: relative; height: 180px; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }
        
        .price-badge { 
            background: #ff4757; 
            color: white; 
            padding: 5px 12px; 
            border-radius: 10px; 
            font-weight: bold; 
            font-size: 0.95rem;
        }

        .btn-remove {
            color: #dc3545;
            background: #fff5f5;
            border: none;
            border-radius: 12px;
            padding: 8px;
            transition: 0.3s;
        }
        .btn-remove:hover { background: #dc3545; color: white; }
        
        .empty-state { padding: 80px 0; }
        .empty-state i { color: #dee2e6; font-size: 5rem; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="page-header shadow">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-800 mb-0"><i class="fa-solid fa-heart text-danger me-3"></i>Favorilerim</h1>
            <p class="opacity-75 mb-0 mt-2">Takip ettiğin ve beğendiğin tüm ilanlar burada.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-danger rounded-pill px-3 py-2 mb-2"><?= $favoriler->num_rows ?> İlan Kayıtlı</span>
            <br>
            <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Vitrine Dön</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="row g-4">
        <?php if($favoriler->num_rows > 0): ?>
            <?php while($f = $favoriler->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-fav shadow-sm">
                        <div class="img-container">
                            <img src="uploads/<?= $f['resim'] ?>" onerror="this.src='https://placehold.jp/400x300.png?text=Resim+Yok'">
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-dark opacity-75 rounded-pill"><?= $f['kategori_adi'] ?></span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark text-truncate mb-2"><?= htmlspecialchars($f['baslik']) ?></h6>
                            <div class="mb-3">
                                <span class="price-badge"><?= number_format($f['fiyat'], 0, ',', '.') ?> ₺</span>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="view.php?id=<?= $f['id'] ?>" class="btn btn-dark btn-sm rounded-pill flex-grow-1 fw-bold">Detayları Gör</a>
                                <a href="favori_islem.php?id=<?= $f['id'] ?>" class="btn-remove" title="Favoriden Çıkar">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center empty-state">
                <i class="fa-solid fa-heart-crack"></i>
                <h3 class="fw-bold text-muted">Henüz favori ilanınız yok</h3>
                <p class="text-secondary">Beğendiğiniz ilanları kalp butonuna basarak buraya ekleyebilirsiniz.</p>
                <a href="index.php" class="btn btn-danger rounded-pill px-5 mt-3 shadow">İlanları Keşfet</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="mt-5 py-4 text-center text-muted border-top bg-white">
    <div class="container">
        <small>&copy; 2025 Ensar İlan - Tüm favorileriniz tek bir yerde.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>