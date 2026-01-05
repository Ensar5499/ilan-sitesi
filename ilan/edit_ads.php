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

// 2. DÜZENLENECEK İLANI BUL
if(!isset($_GET['id'])) { header("Location: ilanlarim.php"); exit; }
$id = (int)$_GET['id'];

// İlanın bu kullanıcıya ait olduğundan emin olalım
$ilan_sorgu = $conn->query("SELECT * FROM ilanlar WHERE id = $id AND kullanici_id = $uid");
if($ilan_sorgu->num_rows == 0) {
    die("Bu ilanı düzenleme yetkiniz yok veya ilan bulunamadı.");
}
$ilan = $ilan_sorgu->fetch_assoc();

// 3. GÜNCELLEME İŞLEMİ (Forma basılınca)
if($_POST){
    $baslik = $conn->real_escape_string($_POST['b']);
    $fiyat = (float)$_POST['f'];
    $aciklama = $conn->real_escape_string($_POST['a']);
    $sehir = $conn->real_escape_string($_POST['s']);

    // SQL Güncelleme
    $sql = "UPDATE ilanlar SET baslik='$baslik', fiyat='$fiyat', aciklama='$aciklama', sehir='$sehir' WHERE id = $id";
    
    if($conn->query($sql)){
        header("Location: ilanlarim.php?durum=guncellendi");
        exit;
    } else {
        $mesaj = "Hata oluştu: " . $conn->error;
    }
}

$kategoriler = $conn->query("SELECT * FROM kategoriler");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>İlanı Düzenle | Ensar İlan</title>
    <style>
        body { background: #f4f7f6; }
        .edit-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-control { border-radius: 10px; padding: 12px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card edit-card p-4">
                <h3 class="fw-bold mb-4 text-center">İlanı Düzenle</h3>
                
                <?php if($mesaj != ""): ?>
                    <div class="alert alert-danger"><?= $mesaj ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">İlan Başlığı</label>
                        <input name="b" type="text" class="form-control" value="<?= htmlspecialchars($ilan['baslik']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fiyat (₺)</label>
                            <input name="f" type="number" class="form-control" value="<?= $ilan['fiyat'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Şehir</label>
                            <input name="s" type="text" class="form-control" value="<?= htmlspecialchars($ilan['sehir']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Açıklama</label>
                        <textarea name="a" class="form-control" rows="5"><?= htmlspecialchars($ilan['aciklama']) ?></textarea>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i> Şimdilik sadece metinleri güncelleyebilirsiniz. Resim değiştirme özelliği yakında!
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">DEĞİŞİKLİKLERİ KAYDET</button>
                        <a href="ilanlarim.php" class="btn btn-light w-100 rounded-pill py-2 fw-bold">İPTAL ET</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>