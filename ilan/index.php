<?php 
session_start(); 
include 'includes/db.php'; 

// --- FAVORİ KONTROL FONKSİYONU ---
function favoriMi($ilan_id, $conn) {
    if(!isset($_SESSION['user_id'])) return false;
    $uid = $_SESSION['user_id'];
    $kontrol = $conn->query("SELECT id FROM favoriler WHERE kullanici_id = $uid AND ilan_id = $ilan_id");
    return ($kontrol && $kontrol->num_rows > 0);
}

// 1. ARAMA VE KATEGORİ SORGUSU
$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$kat_id = isset($_GET['k']) ? (int)$_GET['k'] : 0;

$sql = "SELECT ilanlar.*, kategoriler.isim AS kategori_adi 
        FROM ilanlar 
        LEFT JOIN kategoriler ON ilanlar.kategori_id = kategoriler.id";

$where = [];
if($search) { $where[] = "ilanlar.baslik LIKE '%$search%'"; } 
if($kat_id) { $where[] = "ilanlar.kategori_id = $kat_id"; }
if(count($where) > 0) { $sql .= " WHERE " . implode(" AND ", $where); }
$sql .= " ORDER BY ilanlar.id DESC LIMIT 12";

$ilanlar = $conn->query($sql);
$kategoriler = $conn->query("SELECT * FROM kategoriler");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ensar İlan | Türkiye'nin İlan Platformu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');
        
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .navbar { background: #1e2125 !important; padding: 15px 0; }
        .admin-fast-link { background: #ff4757; color: white; padding: 5px 0; font-size: 13px; text-align: center; }
        
        .search-section { 
            background: linear-gradient(rgba(30, 33, 37, 0.9), rgba(30, 33, 37, 0.9)), url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1000&q=80');
            background-size: cover; background-position: center; padding: 80px 0; color: white; border-radius: 0 0 60px 60px; 
        }

        .card-ilan { border: none; border-radius: 25px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: white; overflow: hidden; position: relative; }
        .card-ilan:hover { transform: translateY(-12px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .image-container { position: relative; }
        .view-count-badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); color: white; padding: 4px 12px; border-radius: 50px; font-size: 12px; backdrop-filter: blur(5px); }
        
        .fav-icon-btn { position: absolute; top: 15px; right: 15px; background: white; color: #ff4757; width: 38px; height: 38px; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer; transition: 0.3s; z-index: 10; }
        .fav-icon-btn:hover { transform: scale(1.15); }

        .price-badge { background: #ff4757; color: white; padding: 7px 18px; border-radius: 12px; font-weight: 800; font-size: 1.1rem; }
        .kat-btn { border-radius: 50px; margin: 5px; padding: 8px 20px; border: 1px solid rgba(255,255,255,0.2); transition: 0.3s; }
        .kat-btn:hover, .kat-btn.active { background: #ff4757 !important; border-color: #ff4757; transform: scale(1.05); }

        /* Yeni Eklenen Mesaj Formu Stili */
        .contact-card { border: none; border-radius: 30px; background: #ffffff; box-shadow: 0 15px 50px rgba(0,0,0,0.05); }
        .form-control-custom { border-radius: 15px; padding: 12px 20px; border: 1px solid #eee; background: #fdfdfd; }
        .form-control-custom:focus { box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1); border-color: #ff4757; }
    </style>
</head>
<body>

<?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
    <div class="admin-fast-link">
        <i class="fas fa-user-shield me-2"></i> Yönetici Girişi Yapıldı. <a href="admin/ensar.php" class="text-white fw-bold ms-2">Panel'e Git</a>
    </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">ENSAR<span class="text-danger">.İLAN</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="ms-auto d-flex align-items-center">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="add_ads.php" class="btn btn-danger rounded-pill px-4 me-3 d-none d-lg-block">
                        <i class="fas fa-plus-circle me-2"></i>Ücretsiz İlan Ver
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-light rounded-pill dropdown-toggle px-4" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?= $_SESSION['kullanici_adi'] ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="profile.php">Profilim & Ayarlar</a></li>
                            <li><a class="dropdown-item py-2" href="ilanlarim.php">İlanlarım</a></li>
                            <li><a class="dropdown-item py-2" href="favorilerim.php">Favorilerim</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="logout.php">Güvenli Çıkış</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="admin/ensar.php" class="text-white-50 text-decoration-none small me-3">
                        <i class="fas fa-lock me-1"></i>Yönetici Girişi
                    </a>
                    <a href="login.php" class="btn btn-outline-light rounded-pill px-4 me-2">Giriş Yap</a>
                    <a href="register.php" class="btn btn-danger rounded-pill px-4 shadow">Hemen Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<section class="search-section text-center">
    <div class="container">
        <h1 class="display-4 fw-800 mb-2">Hayalindeki <span class="text-danger">Geleceği</span> Bul</h1>
        <p class="lead mb-5 opacity-75">Binlerce güncel ilan arasında arama yapmaya başla.</p>
        <form action="index.php" method="GET" class="row g-2 justify-content-center">
            <div class="col-md-7">
                <div class="input-group input-group-lg shadow-lg rounded-pill overflow-hidden bg-white">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-0 px-2" placeholder="Araba, emlak veya eşya ara..." value="<?= $search ?>">
                    <button type="submit" class="btn btn-danger px-5 fw-bold">ARA</button>
                </div>
            </div>
        </form>
        <div class="mt-4">
            <a href="index.php" class="btn btn-sm btn-outline-light kat-btn <?= $kat_id == 0 ? 'active' : '' ?>">Tüm Kategoriler</a>
            <?php while($k = $kategoriler->fetch_assoc()): ?>
                <a href="index.php?k=<?= $k['id'] ?>" class="btn btn-sm btn-outline-light kat-btn <?= $kat_id == $k['id'] ? 'active' : '' ?>"><?= $k['isim'] ?></a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-0">Öne Çıkan İlanlar</h3>
            <p class="text-muted small">Şu an aktif olan en yeni ilanlar listeleniyor.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <?php if($ilanlar && $ilanlar->num_rows > 0): ?>
            <?php while($i = $ilanlar->fetch_assoc()): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card card-ilan h-100 shadow-sm">
                    <div class="image-container">
                        <img src="uploads/<?= $i['resim'] ?>" class="card-img-top" style="height:220px; object-fit:cover;" onerror="this.src='https://placehold.jp/400x300.png?text=Resim+Yok'">
                        <div class="view-count-badge"><i class="fas fa-eye me-1"></i> <?= number_format($i['izlenme']) ?></div>
                        
                        <button class="fav-icon-btn" onclick="favoriIslem(<?= $i['id'] ?>, this)">
                            <i class="<?= favoriMi($i['id'], $conn) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <span class="badge bg-light text-muted border fw-normal mb-2"><?= $i['kategori_adi'] ?></span>
                        <h6 class="fw-bold text-dark mb-3" style="min-height: 40px;"><?= htmlspecialchars($i['baslik']) ?></h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="price-badge"><?= number_format($i['fiyat'], 0, ',', '.') ?> ₺</div>
                            <a href="view.php?id=<?= $i['id'] ?>" class="btn btn-dark btn-sm rounded-pill px-3">İncele</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aradığınız kriterlere uygun ilan bulunamadı.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<section class="py-5" style="background: #f1f3f5;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-4">
                <h3 class="fw-bold">Bize Ulaşın</h3>
                <p class="text-muted">Sorularınız veya önerileriniz için form üzerinden mesaj gönderebilirsiniz.</p>
            </div>
            <div class="col-md-7">
                <div class="card contact-card p-4 p-md-5">
                    <form action="mesaj_gonder.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="isim" class="form-control form-control-custom" placeholder="Adınız Soyadınız" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="eposta" class="form-control form-control-custom" placeholder="E-posta Adresiniz" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="konu" class="form-control form-control-custom" placeholder="Konu" required>
                            </div>
                            <div class="col-12">
                                <textarea name="mesaj" class="form-control form-control-custom" rows="4" placeholder="Mesajınız..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold rounded-pill">MESAJI GÖNDER</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <p class="mb-0 opacity-50">&copy; 2024 Ensar İlan Platformu. Tüm hakları saklıdır.</p>
    </div>
</footer>

<script>
function favoriIslem(ilanId, element) {
    const icon = element.querySelector('i');
    element.style.pointerEvents = "none";

    fetch('favori_ekle.php?id=' + ilanId)
    .then(response => response.text())
    .then(data => {
        if(data === "eklendi") {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
            element.style.transform = "scale(1.3)";
            setTimeout(() => element.style.transform = "scale(1)", 200);
        } else if(data === "cikarildi") {
            icon.classList.remove('fa-solid');
            icon.classList.add('fa-regular');
        } else {
            window.location.href = 'login.php';
        }
    })
    .catch(err => console.error("Hata:", err))
    .finally(() => {
        element.style.pointerEvents = "auto";
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>