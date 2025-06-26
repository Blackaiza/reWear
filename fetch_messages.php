<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) exit();

$chat_with = $_GET['chat_with'];
$me = $_SESSION['no_matrik'];

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ambil semua mesej antara dua pengguna
$stmt = $conn->prepare("SELECT k.*, 
    p.namaPakaian, p.gambar,
    pr.idPertukaran, pr.idPakaianTukar, pt.namaPakaian AS namaPakaianPeminta,
    pr.noMatrikPeminta, pr.noMatrikPemilik,
    u1.nama AS namaPeminta, u2.nama AS namaPemilik,
    perc.idPercuma
    FROM tbl_komunikasi k
    JOIN tbl_pakaian p ON k.idPakaian = p.idPakaian
    LEFT JOIN tbl_percuma perc ON perc.idPakaian = p.idPakaian AND perc.status = 'diterima'
    LEFT JOIN tbl_pertukaran pr ON pr.idPakaianTarget = p.idPakaian AND pr.status = 'diterima'
    LEFT JOIN tbl_pakaian pt ON pr.idPakaianTukar = pt.idPakaian
    LEFT JOIN tbl_pengguna u1 ON u1.noMatrik = pr.noMatrikPeminta
    LEFT JOIN tbl_pengguna u2 ON u2.noMatrik = pr.noMatrikPemilik
    WHERE (k.penghantar = ? AND k.penerima = ?) OR (k.penghantar = ? AND k.penerima = ?)
    ORDER BY k.tarikhMesej ASC");

$stmt->execute([$me, $chat_with, $chat_with, $me]);

$lastPakaianId = null;
$lastDateHeader = null;

echo '<style>
.sticky-date-header {
    position: sticky;
    top: 10px;
    z-index: 999;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
    transition: opacity 0.5s ease;
    opacity: 1;
}
.sticky-date-hidden {
    opacity: 0;
}
</style>';

while ($msg = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $own = $msg['penghantar'] == $me;
    $class = $own ? 'bubble-right' : 'bubble-left';

$currentDate = date('Y-m-d', strtotime($msg['tarikhMesej']));
if ($currentDate != $lastDateHeader) {
    $lastDateHeader = $currentDate;

    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if ($currentDate == $today) {
        $label = "Hari Ini";
    } elseif ($currentDate == $yesterday) {
        $label = "Semalam";
    } else {
        $label = date('d/m/Y', strtotime($msg['tarikhMesej']));
    }

    // Papar date header biasa
echo '<div style="text-align:center; margin:15px 0;">';
echo '<div style="display: inline-block; background:#3c7962; color:#fff; font-weight:600; padding:6px 20px; border-radius:20px; font-size:0.9rem;">' . $label . '</div>';
echo '</div>';

}


    // Bila jumpa pakaian lain, papar header pakaian
    if ($lastPakaianId !== $msg['idPakaian']) {
        $lastPakaianId = $msg['idPakaian'];
        $gambar = explode(',', $msg['gambar'])[0];
        $jenis = !empty($msg['idPakaianTukar']) ? 'pertukaran' : 'percuma';

        echo '<div class="alert alert-light border rounded d-flex align-items-center gap-3 mb-2" style="padding: 10px;">';
        if ($jenis !== 'pertukaran') {
            echo '<img src="' . $gambar . '" width="60" height="60" style="object-fit: cover; border-radius: 10px;">';
        }
        echo '<div>';


        if ($jenis === 'pertukaran') {
            $pakaianPeminta = htmlspecialchars($msg['namaPakaianPeminta'] ?? '-');
            $pakaianPemilik = htmlspecialchars($msg['namaPakaian'] ?? '-');
            echo "<div style='font-size: 0.85rem; margin-top: 5px;'>
                Pakaian Pemohon: $pakaianPeminta<br>
                Pakaian Pemilik: $pakaianPemilik
            </div>";
            echo '<a href="detail_pertukaran.php?id=' . $msg['idPertukaran'] . '" style="color: #3c7962; font-size: 0.8rem; font-weight: 500; text-decoration: none;" 
            onmouseover="this.style.textDecoration=\'underline\'" 
            onmouseout="this.style.textDecoration=\'none\'">[Butiran Pertukaran]</a>';
        } else {
            echo '<span style="font-weight: 500; font-size: 0.85rem; color:rgb(98, 108, 104);">' . htmlspecialchars($msg['namaPakaian']) . '</span><br>';
            echo '<a href="detail_percuma.php?id=' . $msg['idPercuma'] . '" style="color: #3c7962; font-weight: 500; font-size: 0.8rem; text-decoration: none;" 
            onmouseover="this.style.textDecoration=\'underline\'" 
            onmouseout="this.style.textDecoration=\'none\'">[Butiran Pemberian]</a>';
        }

        echo '</div></div>';
    }



    echo '<div class="bubble ' . $class . '">';
    echo htmlspecialchars($msg['kandunganMesej']);
    echo '<div style="font-size: 0.75rem; color: #5f7161; margin-top: 5px; ' . ($own ? 'text-align:right;' : 'text-align:left;') . '">';
    echo date('g:i A', strtotime($msg['tarikhMesej']));
    echo '</div>';
    echo '</div>';
}
echo '<div id="scroll-bottom"></div>';

?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let stickyHeaders = document.querySelectorAll(".sticky-date-header");
    let timer = null;

    let isScrolling;

    window.addEventListener('scroll', function() {
        stickyHeaders.forEach(header => {
            header.classList.remove('sticky-date-hidden');
        });

        window.clearTimeout(isScrolling);
        isScrolling = setTimeout(function() {
            stickyHeaders.forEach(header => {
                header.classList.add('sticky-date-hidden');
            });
        }, 1000);
    });

    stickyHeaders.forEach(header => {
        header.classList.add('sticky-date-hidden');
    });

    // ✅ Auto scroll ke bawah bila mesej dibuka
    var scrollTarget = document.getElementById("scroll-bottom");
    if (scrollTarget) {
        scrollTarget.scrollIntoView({ behavior: "auto" }); // guna "smooth" kalau nak animasi
    }
});
</script>

