<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db.php';

// Giriş yapılmışsa döngüye girmemesi için JS ile yönlendiriyoruz
if(isset($_SESSION['user_id'])){
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$hata = "";
$mesaj = "";

if(isset($_GET['kayit']) && $_GET['kayit'] == 'basarili'){
    $mesaj = "Başarıyla kayıt oldunuz! Şimdi giriş yapabilirsiniz.";
}

if(isset($_POST['giris_yap'])){
    $email = $conn->real_escape_string(trim($_POST['email']));
    $sifre = trim($_POST['sifre']);

    $sorgu = $conn->query("SELECT * FROM kullanicilar WHERE email = '$email' AND sifre = '$sifre'");
    
    if($sorgu && $sorgu->num_rows > 0){
        $user = $sorgu->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
        $_SESSION['rol'] = $user['rol']; 
        
        session_write_close();
        echo "<script>window.location.href='index.php';</script>";
        exit();
    } else {
        $hata = "E-posta veya şifre hatalı!";
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
    <title>Giriş Yap | Ensar İlan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        body { background: linear-gradient(135deg, #1e2125 0%, #ff4757 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Inter', sans-serif; margin:0; }
        .login-card { background: white; padding: 40px; border-radius: 25px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); width: 100%; max-width: 420px; margin: auto; }
        .form-control { border-radius: 12px; padding: 12px; background-color: #f8f9fa; border: 1px solid #eee; }
        .btn-danger { background-color: #ff4757; border: none; border-radius: 12px; padding: 12px; font-weight: 700; transition: 0.3s; }
        .btn-danger:hover { background-color: #e84118; transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-extrabold text-dark">Tekrar Hoş Geldin</h2>
            <p class="text-muted small">İlanlarını yönetmek için giriş yap</p>
        </div>
        
        <?php if($hata != ""): ?><div class="alert alert-danger py-2 small text-center"><?= $hata ?></div><?php endif; ?>
        <?php if($mesaj != ""): ?><div class="alert alert-success py-2 small text-center"><?= $mesaj ?></div><?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">E-posta Adresi</label>
                <input type="email" name="email" class="form-control" placeholder="mail@ensar.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Şifre</label>
                <input type="password" name="sifre" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="giris_yap" class="btn btn-danger w-100 shadow-sm">GİRİŞ YAP</button>
        </form>

        <div class="mt-4 text-center">
            <p class="small text-muted mb-2">Henüz hesabın yok mu?</p>
            <a href="register.php" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold text-decoration-none">Hemen Kayıt Ol</a>
        </div>
        <div class="mt-4 border-top pt-3 text-center">
            <a href="index.php" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Vitrine Geri Dön</a>
        </div>
    </div>
</div>
</body>
</html>