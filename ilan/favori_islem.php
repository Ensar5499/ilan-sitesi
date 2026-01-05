<?php
include 'includes/db.php';
session_start();

// 1. Giriş kontrolü
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$u_id = $_SESSION['user_id'];
// Gelen ID'yi POST veya GET üzerinden alabiliriz (POST daha güvenlidir)
$i_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$status = "";

if($i_id > 0) {
    // 2. Mevcut durumu kontrol et
    $kontrol = $conn->query("SELECT id FROM favoriler WHERE kullanici_id = $u_id AND ilan_id = $i_id");

    if($kontrol->num_rows > 0){
        // Zaten favorideyse: SİL
        if($conn->query("DELETE FROM favoriler WHERE kullanici_id = $u_id AND ilan_id = $i_id")) {
            $status = "removed";
        }
    } else {
        // Favoride değilse: EKLE
        if($conn->query("INSERT INTO favoriler (kullanici_id, ilan_id) VALUES ($u_id, $i_id)")) {
            $status = "added";
        }
    }
}

// 3. Yönlendirme ve Bildirim Parametresi
// Kullanıcıyı geldiği sayfaya geri gönderirken sonuna işlem durumunu ekliyoruz
$yonlen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

// URL'de zaten ? varsa & ile, yoksa ? ile ekle
$ayrac = (strpos($yonlen, '?') !== false) ? '&' : '?';
header("Location: " . $yonlen . $ayrac . "fav_status=" . $status);
exit;
?>