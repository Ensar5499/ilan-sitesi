<?php 
session_start(); 
include '../includes/db.php'; 

// Admin kontrolü
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Mesajları çekelim
$mesajlar = $conn->query("SELECT * FROM destek_mesajlari ORDER BY tarih DESC");

// Mesaj Silme İşlemi
if(isset($_GET['sil'])) {
    $id = $_GET['sil'];
    $conn->query("DELETE FROM destek_mesajlari WHERE id = $id");
    header("Location: mesajlar.php");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesajlar | Ensar Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        .sidebar { background: #1a1d20; min-height: 100vh; color: white; padding-top: 30px; position: fixed; width: 16.66667%; }
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 10px; padding: 12px 20px; border-radius: 12px; }
        .nav-link.active { background: #ff4757; color: white; }
        .main-content { margin-left: 16.66667%; padding: 40px; }
        .message-card { border: none; border-radius: 15px; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; transition: 0.3s; }
        .message-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar px-4">
            <h3 class="fw-bold mb-5 text-center text-danger">ENSAR<span class="text-white">.AD</span></h3>
            <ul class="nav flex-column">
                <li><a href="ensar.php" class="nav-link"><i class="fas fa-home me-2"></i> Özet</a></li>
                <li><a href="mesajlar.php" class="nav-link active"><i class="fas fa-envelope me-2"></i> Mesajlar</a></li>
                <li><a href="../index.php" class="nav-link"><i class="fas fa-eye me-2"></i> Siteyi Gör</a></li>
            </ul>
        </div>

        <div class="col-md-10 main-content">
            <h2 class="fw-bold mb-4">Gelen Mesajlar</h2>
            
            <?php if($mesajlar->num_rows > 0): ?>
                <?php while($m = $mesajlar->fetch_assoc()): ?>
                <div class="card message-card p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold text-danger mb-1"><?= htmlspecialchars($m['konu']) ?></h5>
                            <small class="text-muted"><i class="fas fa-user me-1"></i> <?= htmlspecialchars($m['isim']) ?> (<?= htmlspecialchars($m['eposta']) ?>)</small>
                        </div>
                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($m['tarih'])) ?></small>
                    </div>
                    <hr>
                    <p class="text-dark"><?= nl2br(htmlspecialchars($m['mesaj'])) ?></p>
                    <div class="text-end">
                        <a href="?sil=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu mesajı silmek istiyor musun?')">
                            <i class="fas fa-trash me-1"></i> Sil
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">Henüz hiç mesaj gelmemiş.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>