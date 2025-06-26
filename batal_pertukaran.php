<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idPertukaran'])) {
    $idPertukaran = $_POST['idPertukaran'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Dapatkan id pakaian supaya statusnya dikemaskini semula
        $stmt = $conn->prepare("SELECT idPakaianTarget, idPakaianTukar FROM tbl_pertukaran WHERE idPertukaran = ? AND status = 'menunggu'");
        $stmt->execute([$idPertukaran]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Padam permintaan
            $stmt = $conn->prepare("DELETE FROM tbl_pertukaran WHERE idPertukaran = ?");
            $stmt->execute([$idPertukaran]);

            // Kembalikan status pakaian ke 'Tersedia'
            $stmt = $conn->prepare("UPDATE tbl_pakaian SET status = 'Tersedia' WHERE idPakaian IN (?, ?)");
            $stmt->execute([$row['idPakaianTarget'], $row['idPakaianTukar']]);
        }

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
}
