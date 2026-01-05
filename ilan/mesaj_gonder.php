<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isim = $conn->real_escape_string($_POST['isim']);
    $eposta = $conn->real_escape_string($_POST['eposta']);
    $konu = $conn->real_escape_string($_POST['konu']);
    $mesaj = $conn->real_escape_string($_POST['mesaj']);

    $sql = "INSERT INTO destek_mesajlari (isim, eposta, konu, mesaj) VALUES ('$isim', '$eposta', '$konu', '$mesaj')";

    if ($conn->query($sql)) {
        echo "<script>alert('Mesajınız admin panelimize iletildi. Teşekkürler!'); window.location.href='index.php';</script>";
    } else {
        echo "Hata oluştu: " . $conn->error;
    }
}
?>