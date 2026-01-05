<?php include '../includes/db.php'; session_start();
if($_SESSION['rol'] == 'admin'){ $id = $_GET['id']; $conn->query("DELETE FROM ilanlar WHERE id=$id"); }
header("Location: ensar.php"); ?>