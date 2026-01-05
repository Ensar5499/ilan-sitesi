<?php 
include 'includes/db.php'; 
session_start();
$uid = $_SESSION['user_id'];

if($_POST){
    $yeni_ad = $_POST['k_ad'];
    $yeni_sifre = $_POST['sifre'];
    
    if(!empty($yeni_sifre)){
        $sql = "UPDATE kullanicilar SET kullanici_ad = '$yeni_ad', sifre = '$yeni_sifre' WHERE id = $uid";
    } else {
        $sql = "UPDATE kullanicilar SET kullanici_ad = '$yeni_ad' WHERE id = $uid";
    }
    
    if($conn->query($sql)){
        $_SESSION['kullanici_ad'] = $yeni_ad;
        $mesaj = "Bilgiler başarıyla güncellendi!";
    }
}

$kullanici = $conn->query("SELECT * FROM kullanicilar WHERE id = $uid")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container py-5" style="max-width: 500px;">
    <div class="card p-4 shadow border-0 rounded-4">
        <h4 class="fw-bold mb-4">Profil Ayarları</h4>
        <?php if(isset($mesaj)) echo "<div class='alert alert-success'>$mesaj</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="small fw-bold">Kullanıcı Adı</label>
                <input type="text" name="k_ad" class="form-control" value="<?= $kullanici['kullanici_ad'] ?>" required>
            </div>
            <div class="mb-4">
                <label class="small fw-bold">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                <input type="password" name="sifre" class="form-control" placeholder="******">
            </div>
            <button class="btn btn-danger w-100 fw-bold py-2">GÜNCELLE</button>
            <a href="profil.php" class="btn btn-link w-100 text-muted mt-2">Geri Dön</a>
        </form>
    </div>
</div>
</body>
</html>