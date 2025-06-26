<?php
session_start();
if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_level'] !== 'pentadbir') {
    header("Location: indexnu.php");
    exit();
}

include 'database.php';
// DB connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'Lelaki';  // Default kategori Lelaki

$queryUpload = $conn->prepare("
    SELECT MONTH(tarikh_muatnaik) AS bulan, COUNT(*) AS total_upload
    FROM tbl_pakaian pk
    WHERE pk.kategori = ?
    GROUP BY bulan
    ORDER BY bulan ASC
");

$queryUpload->execute([$kategori]);  
$dataUpload = $queryUpload->fetchAll(PDO::FETCH_ASSOC);

// Query untuk jumlah pakaian yang telah diberi (percuma + pertukaran)
$queryDiberi = $conn->prepare("
    SELECT bulan, SUM(jumlah) AS total_diberi FROM (
        SELECT MONTH(p.tarikh_minta) AS bulan, COUNT(*) AS jumlah
        FROM tbl_percuma p
        JOIN tbl_pakaian pk ON p.idPakaian = pk.idPakaian
        WHERE p.status = 'Diterima' AND pk.kategori = ?
        GROUP BY bulan

        UNION ALL

        SELECT MONTH(t.tarikhPermintaan) AS bulan, COUNT(*) AS jumlah
        FROM tbl_pertukaran t
        JOIN tbl_pakaian pk ON t.idPakaianTarget = pk.idPakaian
        WHERE t.status = 'diterima' AND pk.kategori = ?
        GROUP BY bulan
    ) AS gabungan
    GROUP BY bulan
    ORDER BY bulan ASC
");
$queryDiberi->execute([$kategori, $kategori]);
$dataDiberi = $queryDiberi->fetchAll(PDO::FETCH_ASSOC);

// Pemetaan nombor bulan kepada nama bulan penuh
$bulan_penuh = ["Januari", "Februari", "Mac", "April", "Mei", "Jun", "Julai", "Ogos", "September", "Oktober", "November", "Disember"];
$upload = array_fill(0, 12, 0);
$diberi = array_fill(0, 12, 0);

// Masukkan data upload (dari tbl_pakaian)
foreach ($dataUpload as $row) {
    $index = (int)$row['bulan'] - 1;  // Tukar bulan daripada '05' menjadi 4 (Mei)
    if ($index >= 0 && $index < 12) {
        $upload[$index] = (int)$row['total_upload'];  // Jumlah pakaian dimuat naik
    }
}

// Masukkan data diberi (pakaian yang diberikan percuma dan pertukaran)
foreach ($dataDiberi as $row) {
    $index = (int)$row['bulan'] - 1;  // Tukar bulan daripada '05' menjadi 4 (Mei)
    if ($index >= 0 && $index < 12) {
        $diberi[$index] = (int)$row['total_diberi'];  // Jumlah pakaian yang telah diberi
    }
}

// Hantar data ke frontend dalam format JSON
echo json_encode([
    "labels" => $bulan_penuh,
    "upload" => $upload,  // Jumlah pakaian yang dimuat naik
    "diberi" => $diberi,  // Jumlah pakaian yang telah diberi
    "katalog" => $upload  // Data untuk Pakaian Dimuat Naik (sama dengan $upload)
]);

?>
