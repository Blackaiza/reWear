<?php
session_start();
if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>FAQ - ReWear</title>
    <!-- Bootstrap 4.6 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #3c7962;
            font-weight: 700;
            margin-bottom: 30px;
        }
        .faq-section {
            margin-top: 30px;
        }
        .faq-question {
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 15px;
            cursor: pointer;
        }
        .faq-answer {
            display: none;
            font-size: 1rem;
            margin-left: 20px;
            margin-top: 10px;
            padding-left: 10px;
            border-left: 3px solid #3c7962;
        }
        .faq-question:hover {
            color: #3c7962;
        }
        .active + .faq-answer {
            display: block;
        }
    </style>
</head>
<body>
<?php include 'nav_bar_nu.php'; ?>

<div class="container">
    <h1>FAQ - Soalan Lazim</h1>

    <div class="faq-section">
        <div class="faq-question">Apa itu ReWear?</div>
        <div class="faq-answer">ReWear ialah sistem pengurusan pertukaran dan pemberian pakaian terpakai dalam kalangan pengguna yang berdaftar. Pengguna boleh memuat naik pakaian, menawarkan pertukaran, serta membuat permintaan pakaian dari pengguna lain.</div>

        <div class="faq-question">Bagaimana cara untuk memuat naik pakaian?</div>
        <div class="faq-answer">
            - Klik butang "+ Muat Naik Pakaian Baru".<br>
            - Lengkapkan maklumat pakaian (nama, saiz, jenama, kategori, jenis pemberian, gambar).<br>
            - Klik "Muat Naik".
        </div>

        <div class="faq-question">Bagaimana cara untuk buat permintaan pertukaran?</div>
        <div class="faq-answer">
            1. Semak katalog pakaian.<br>
            2. Pilih pakaian yang bertanda pertukaran.<br>
            3. Klik "Pilih pakaian untuk ditukar".<br>
            4. Isi borang pertukaran.<br>
            5. Hantar permintaan kepada pemilik.
        </div>
        <div class="faq-question">Apa yang perlu saya lakukan selepas membuat permintaan?</div>
        <div class="faq-answer">
            Selepas membuat permintaan, anda perlu menunggu pemilik pakaian untuk menerima atau menolak permintaan anda.<br>
            Anda boleh menyemak <strong>status permintaan</strong> dengan menekan <strong>"Permintaan"</strong> yang terletak di bahagian bar navigasi.<br>
        </div>
        <div class="faq-question">Bagaimana cara untuk berkomunikasi dengan pemilik pakaian?</div>
        <div class="faq-answer">
            Selepas permintaan anda diterima, butang mesej akan muncul. Anda boleh berbincang dengan pemilik pakaian melalui fungsi mesej dalam sistem untuk menetapkan masa dan lokasi penyerahan pakaian.
        </div>

        <div class="faq-question">Bilakah butang mesej akan muncul?</div>
        <div class="faq-answer">
             Butang mesej hanya akan dipaparkan selepas pemilik pakaian telah menerima permintaan.<br>
             Selepas diterima, anda dan pemilik boleh mula berbincang melalui fungsi mesej dalam sistem.
        </div>

        <div class="faq-question">Bagaimana proses penyerahan pakaian berlaku?</div>
        <div class="faq-answer">
             1. Selepas permintaan diterima, kedua-dua pihak akan berkomunikasi melalui fungsi mesej.<br>
             2. Mereka akan tetapkan masa, lokasi dan cara serahan pakaian.<br>
             3. Penyerahan pakaian berlaku secara manual, ReWear hanya sebagai penghubung komunikasi.
        </div>

        <div class="faq-question">Apa maksud status permintaan?</div>
        <div class="faq-answer">
            <strong>Menunggu:</strong> Permintaan sedang diproses.<br>
            <strong>Diterima:</strong> Permintaan anda telah diluluskan.<br>
            <strong>Ditolak:</strong> Permintaan ditolak oleh pemilik pakaian.
        </div>

        <div class="faq-question">Bolehkah saya membatalkan permintaan yang telah dihantar?</div>
        <div class="faq-answer">
             Ya, selagi status masih <strong>"Menunggu"</strong>, anda boleh membatalkan permintaan.
        </div>

        <div class="faq-question">Jika permintaan saya ditolak, bolehkah saya membuat permintaan sekali lagi?</div>
        <div class="faq-answer">
            Ya, anda boleh membuat permintaan sekali lagi <strong>sekiranya pakaian tersebut masih tersedia</strong> dan belum diterima oleh pengguna lain.<br>
            Sila pastikan anda memahami sebab penolakan (jika ada) dan patuhi garis panduan sebelum menghantar permintaan baru.
        </div>
    </div>

</div>

<script>
    document.querySelectorAll('.faq-question').forEach(function(question) {
        question.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
</script>
</body>
</html>
