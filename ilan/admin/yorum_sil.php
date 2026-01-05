<?php
session_start();
include '../includes/db.php';

// Güvenlik: Sadece admin silebilir
if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin' && isset($_GET['id'])){
    $id = (int)$_GET['id'];
    $ilan_id = (int)$_GET['ilan_id'];
    
    // Yorumu sil
    $conn->query("DELETE FROM yorumlar WHERE id = $id");
    
    // Silme işleminden sonra ilana geri dön
    header("Location: ../view.php?id=$ilan_id");
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>