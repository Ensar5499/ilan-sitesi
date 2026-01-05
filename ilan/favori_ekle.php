<?php
include 'includes/db.php';
session_start();

// Giriş yapılmamışsa işlem yapma
if(!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo "hata";
    exit;
}

$uid = $_SESSION['user_id'];
$iid = (int)$_GET['id'];

// Bu ilan zaten favoride mi?
$kontrol = $conn->query("SELECT id FROM favoriler WHERE kullanici_id = $uid AND ilan_id = $iid");

if($kontrol->num_rows > 0) {
    // Varsa favoriden çıkar
    $conn->query("DELETE FROM favoriler WHERE kullanici_id = $uid AND ilan_id = $iid");
    echo "cikarildi";
} else {
    // Yoksa favoriye ekle
    $conn->query("INSERT INTO favoriler (kullanici_id, ilan_id) VALUES ($uid, $iid)");
    echo "eklendi";
}
?>