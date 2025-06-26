<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $noMatrik = $_SESSION['no_matrik'];
    $idPakaian = $_POST['idPakaian'] ?? null;

    if ($idPakaian) {
        // 1. Semak mesej pertama berdasarkan idPakaian
        $stmtFirst = $conn->prepare("SELECT * FROM tbl_komunikasi WHERE idPakaian = ? ORDER BY tarikhMesej ASC LIMIT 1");
        $stmtFirst->execute([$idPakaian]);
        $firstMessage = $stmtFirst->fetch();

        // 2. Jika pengguna ialah penghantar mesej pertama
        if ($firstMessage && $firstMessage['penghantar'] === $noMatrik) {
            // 3. Kemas kini status pakaian
            $update = $conn->prepare("UPDATE tbl_pakaian SET status = 'Tersedia' WHERE idPakaian = ?");
            $update->execute([$idPakaian]);

            // (Optional) Hantar notifikasi mesej kepada pemilik
            $stmtOwner = $conn->prepare("SELECT noMatrik FROM tbl_pakaian WHERE idPakaian = ?");
            $stmtOwner->execute([$idPakaian]);
            $owner = $stmtOwner->fetchColumn();

            $insertNote = $conn->prepare("INSERT INTO tbl_komunikasi (idPakaian, penghantar, penerima, kandunganMesej, sudah_dibaca) 
                                          VALUES (?, ?, ?, ?, 0)");
            $insertNote->execute([
                $idPakaian,
                $noMatrik,
                $owner,
                'Pengguna telah membatalkan minat terhadap pakaian ini.'
            ]);
        } else {
            // Pengguna bukan penghantar pertama — tidak dibenarkan batal
            $_SESSION['error'] = 'Anda tidak dibenarkan membatalkan minat kerana anda bukan penghantar pertama.';
        }
    }

    // Redirect balik ke paparan mesej
    header("Location: paparan_mesej.php?idPakaian=$idPakaian");
    exit();

} catch (PDOException $e) {
    echo "Ralat: " . $e->getMessage();
}
?>
