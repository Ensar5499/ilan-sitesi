<?php 
include 'includes/db.php';
session_start();

// Eğer kullanıcı giriş yapmışsa ana sayfaya yönlendir
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$mesaj = "";

if($_POST){
    // Formdan gelen verileri güvenli hale getiriyoruz
    $a = $conn->real_escape_string($_POST['a']); 
    $e = $conn->real_escape_string($_POST['e']); 
    $s = $_POST['s']; // Şifreleme istersen md5($_POST['s']) yapabilirsin
    
    // Senin tablondaki sütun isimlerine göre güncellendi: kullanici_adi, email, sifre, rol
    $ekle = $conn->query("INSERT INTO kullanicilar (kullanici_adi, email, sifre, rol) VALUES ('$a', '$e', '$s', 'user')");
    
    if($ekle){
        header("Location: login.php?kayit=basarili");
        exit();
    } else {
        $mesaj = "Kayıt sırasında bir hata oluştu: " . $conn->error;
    }
} 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Hesap Oluştur | Ensar İlan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        
        body { 
            background: linear-gradient(135deg, #1e2125 0%, #ff4757 100%); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            font-family: 'Inter', sans-serif;
        }
        .reg-card { 
            background: white; 
            border-radius: 25px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.3); 
            overflow: hidden;
        }
        .form-control { 
            border-radius: 12px; 
            padding: 12px 15px; 
            background-color: #f8f9fa;
            border: 1px solid #eee;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ff4757;
            background-color: white;
        }
        .btn-register {
            background-color: #ff4757;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-register:hover {
            background-color: #e84118;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 71, 87, 0.4);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="reg-card p-5">
                <div class="text-center mb-4">
                    <h2 class="fw-extrabold text-dark">Kayıt Ol</h2>
                    <p class="text-muted">Hemen ilan vermeye başla!</p>
                </div>

                <?php if($mesaj != ""): ?>
                    <div class="alert alert-danger small py-2"><?= $mesaj ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kullanıcı Adı</label>
                        <input name="a" class="form-control" placeholder="Örn: Ensar34" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">E-posta</label>
                        <input name="e" type="email" class="form-control" placeholder="mail@adresin.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Şifre Belirle</label>
                        <input name="s" type="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-register w-100 shadow">HESABI OLUŞTUR</button>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted mb-0">Zaten üye misin? 
                            <a href="login.php" class="text-danger fw-bold text-decoration-none">Giriş Yap</a>
                        </p>
                    </div>
                </form>
            </div>
            <div class="text-center mt-3">
                <a href="index.php" class="text-white-50 small text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Vitrine Geri Dön</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>