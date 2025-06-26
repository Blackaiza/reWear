<?php
session_start();
include 'database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['no_matrik'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$penghantar = $_SESSION['no_matrik'];
$penerima = trim($_POST['receiver_id']);
$idPakaian = trim($_POST['pakaian_id']);
$kandungan = trim($_POST['content']);

if ($penghantar === $penerima) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak boleh mesej diri sendiri']);
    exit();
}

if ($kandungan !== '' && !empty($penerima) && !empty($idPakaian)) {
    $stmt = $conn->prepare("INSERT INTO tbl_komunikasi (idPakaian, penghantar, penerima, kandunganMesej, sudah_dibaca, tarikhMesej) VALUES (?, ?, ?, ?, 0, NOW())");
    $stmt->execute([$idPakaian, $penghantar, $penerima, $kandungan]);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Maklumat tidak lengkap atau mesej kosong']);
}
?>
