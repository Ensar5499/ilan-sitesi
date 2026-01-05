<?php 
include 'includes/db.php'; 
session_start();

// 1. GİRİŞ KONTROLÜ
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

// Kategorileri çekiyoruz
$kategoriler = $conn->query("SELECT id, isim FROM kategoriler");

$mesaj = "";
$mesaj_tur = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Verileri al ve temizle
    $baslik = $conn->real_escape_string($_POST['b']);
    $fiyat = (float)$_POST['f'];
    $aciklama = $conn->real_escape_string($_POST['a']);
    $kat_id = (int)$_POST['k']; 
    $sehir = $conn->real_escape_string($_POST['s']); 
    $uid = $_SESSION['user_id'];
    
    // Resim yükleme işlemi
    $resim_adi = "varsayilan.jpg"; 
    $hata_varmi = false;

    if(!empty($_FILES['r']['name'])){
        $dosya_yolu = "uploads/";
        if (!file_exists($dosya_yolu)) { mkdir($dosya_yolu, 0777, true); }
        
        $uzanti = strtolower(pathinfo($_FILES['r']['name'], PATHINFO_EXTENSION));
        $izin_verilenler = ['jpg', 'jpeg', 'png', 'webp'];

        if(in_array($uzanti, $izin_verilenler)){
            // Çakışma olmaması için benzersiz isim
            $resim_adi = "ensar_" . time() . "_" . uniqid() . "." . $uzanti;
            if(!move_uploaded_file($_FILES['r']['tmp_name'], $dosya_yolu . $resim_adi)){
                $mesaj = "Resim yüklenirken bir sorun oluştu!";
                $mesaj_tur = "danger";
                $hata_varmi = true;
            }
        } else {
            $mesaj = "Sadece JPG, PNG ve WEBP formatları yüklenebilir!";
            $mesaj_tur = "warning";
            $hata_varmi = true;
        }
    }

    if(!$hata_varmi){
        // Veritabanına Kayıt
        $sql = "INSERT INTO ilanlar (baslik, aciklama, fiyat, resim, kategori_id, kullanici_id, sehir) 
                VALUES ('$baslik', '$aciklama', '$fiyat', '$resim_adi', '$kat_id', '$uid', '$sehir')";
        
        if($conn->query($sql)){
            // BAŞARILI: F5 yapmana gerek kalmadan ilanlarım sayfasına atar
            header("Location: ilanlarim.php?durum=eklendi");
            exit;
        } else {
            $mesaj = "Veritabanı hatası: " . $conn->error;
            $mesaj_tur = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>İlan Ver | Ensar İlan</title>
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .add-card { border: none; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); background: white; }
        .form-label { font-weight: 700; color: #333; font-size: 0.9rem; text-transform: uppercase; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 2px solid #eee; transition: 0.3s; }
        .form-control:focus { border-color: #ff4757; box-shadow: none; background: #fff; }
        .btn-submit { background: linear-gradient(45deg, #ff4757, #ff6b81); color: white; border: none; padding: 15px; border-radius: 15px; font-weight: 800; letter-spacing: 1px; }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(255, 71, 87, 0.4); color: white; }
        .upload-area { border: 2px dashed #ccc; padding: 40px; text-align: center; border-radius: 20px; cursor: pointer; background: #fafafa; transition: 0.3s; }
        .upload-area:hover { border-color: #ff4757; background: #fff5f6; }
        #previewImage { max-height: 180px; border-radius: 15px; display: none; margin-top: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="text-center mb-4">
                <i class="fa-solid fa-rocket fa-3x text-danger mb-3"></i>
                <h2 class="fw-black" style="font-weight: 900;">YENİ İLAN OLUŞTUR</h2>
                <p class="text-muted">Hızlıca bilgileri doldur, satışa başla!</p>
            </div>

            <div class="card add-card p-4 p-md-5">
                <?php if($mesaj != ""): ?>
                    <div class="alert alert-<?= $mesaj_tur ?> border-0 shadow-sm rounded-4"><?= $mesaj ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label">İlan Başlığı</label>
                        <input name="b" type="text" class="form-control" placeholder="Örn: Tertemiz iPhone 13 Pro" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Kategori</label>
                            <select name="k" class="form-select" required>
                                <option value="">Seçim Yapın</option>
                                <?php while($k = $kategoriler->fetch_assoc()): ?>
                                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['isim']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Şehir</label>
                            <input name="s" type="text" class="form-control" placeholder="Örn: İstanbul" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Fiyat (TL)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;"><i class="fa-solid fa-lira-sign text-success"></i></span>
                            <input name="f" type="number" class="form-control border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Açıklama</label>
                        <textarea name="a" class="form-control" rows="4" placeholder="Ürünün özelliklerini buraya yazın..."></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Ürün Fotoğrafı</label>
                        <div class="upload-area" onclick="document.getElementById('fileInput').click();">
                            <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted mb-2"></i>
                            <p class="mb-0 fw-bold text-muted">Fotoğraf Seçmek İçin Dokun</p>
                            <input type="file" name="r" id="fileInput" class="d-none" onchange="previewFile(this)" required>
                            <img id="previewImage" src="">
                            <div id="fileName" class="mt-2 small text-danger fw-bold"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit w-100">
                        İLANIMI YAYINA AL <i class="fa-solid fa-paper-plane ms-2"></i>
                    </button>
                </form>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none text-muted fw-bold">
                    <i class="fa-solid fa-xmark me-1"></i> Vazgeç ve Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFile(input) {
        const file = input.files[0];
        const preview = document.getElementById('previewImage');
        const fileNameDiv = document.getElementById('fileName');

        if (file) {
            fileNameDiv.innerHTML = 'Seçilen: ' + file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
            }
            reader.readAsDataURL(file);
        }
    }
</script>

</body>
</html>