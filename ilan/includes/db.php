<?php
$conn = new mysqli("localhost", "root", "", "ilansitesi");
$conn->set_charset("utf8");
if ($conn->connect_error) { die("Bağlantı hatası: " . $conn->connect_error); }
?>