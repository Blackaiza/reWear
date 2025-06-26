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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear UKM - Sistem Pakaian Terpakai</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fefefe;
        }

        .navbar {
            background-color: #3c7962;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .nav-link:hover {
            color: #e2e2e2 !important;
        }

        .landing-section {
            background-image: url('dashboard1.jpg'); /* Initial background */
            background-size: cover;
            background-position: bottom center;
            background-repeat: no-repeat;
            color: white;
            padding: 61px 0;
            position: relative;
            overflow: hidden;
        }

        .landing-section:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .landing-section .container {
            position: relative;
            z-index: 2;
        }

        .landing-section img {
            max-width: 100%;
            height: auto;
            max-height: 360px;
            object-fit: contain;
        }

        .cta-btn {
            background: #ffffff;
            color: #000;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .cta-btn:hover {
            background: #f1f1f1;
            transform: scale(1.05);
        }

        .feature-box {
            background: white;
            border: 1px solid #eaeaea;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 2.8rem;
            color: #3c7962;
        }

        .feature-title {
            font-weight: 600;
            margin-top: 15px;
        }

        .about-section {
            background-color: #fafafa;
            padding: 80px 0;
        }

        .about-section p {
            font-size: 1.1rem;
            color: #444;
        }

        footer {
            background: #3c7962;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        footer a {
            color: #ccc;
            text-decoration: none;
        }

        footer a:hover {
            color: white;
        }


.transition-images {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1; /* Ensure it stays behind the content */
    animation: fadeBackground 15s infinite; /* Infinite loop for background change */
}

@keyframes fadeBackground {
    0% {
        opacity: 0;
        background-image: url('dashboard1.jpg'); /* Initial background */
    }
    25% {
        opacity: 1;
        background-image: url('bg.jpg'); /* Second background image */
    }
    50% {
        opacity: 0;
        background-image: url('bg.jpg'); /* Hold the second image for a while */
    }
    75% {
        opacity: 1;
        background-image: url('bg2.jpg'); /* Third background image */
    }
    100% {
        opacity: 0;
        background-image: url('bg2.jpg'); /* Hold the third image for a while */
    }
}

</style>
</head>
<body>

<!-- Navbar -->
<?php include 'nav_bar_nu.php'; ?>

<section class="landing-section">
    <div class="transition-images"></div> <!-- This div controls the background image transition -->
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-md-start" data-aos="fade-up">
                <h1 class="display-4 fw-bold">Selamat Datang ke ReWear</h1>
                <p class="lead">Platform pertukaran dan pemberian pakaian terpakai untuk komuniti UKM.</p>
                <a href="muatnaik_form.php" class="cta-btn mt-3 d-inline-block">Mulakan Sekarang</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="container py-5" data-aos="fade-up">
    <h2 class="text-center fw-bold" style="color:#3c7962;">Ciri-ciri Kami</h2>
    <div class="row mt-5">
        <div class="col-md-4 mb-4" data-aos="fade-up">
            <div class="feature-box h-100">
                <i class="fas fa-tshirt feature-icon"></i>
                <h4 class="feature-title">Permohonan Pakaian</h4>
                <p>Mohon pakaian terpakai yang ditawarkan oleh rakan UKM dengan mudah.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="feature-box h-100">
                <i class="fas fa-gift feature-icon"></i>
                <h4 class="feature-title">Pemberian Pakaian</h4>
                <p>Kongsi pakaian anda yang tidak lagi digunakan dengan warga UKM.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="feature-box h-100">
                <i class="fas fa-comments feature-icon"></i>
                <h4 class="feature-title">Mesej Langsung</h4>
                <p>Berhubung terus dengan pemberi atau pemohon untuk urusan penyerahan.</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section" data-aos="fade-up">
    <div class="container">
        <h2 class="text-center fw-bold mb-4" style="color:#3c7962;">Tentang ReWear</h2>
        <p class="text-center">ReWear adalah platform rasmi UKM yang memudahkan pertukaran dan pemberian pakaian terpakai dalam kalangan pelajar dan staf UKM. Kami menyokong budaya kitar semula dan saling membantu dalam komuniti. Ayuh bersama-sama memupuk gaya hidup lestari dan bertanggungjawab.</p>
    </div>
</section>


<!-- Benefits Section -->
<section id="benefits" class="container py-5">
    <h2 class="text-center fw-bold" style="color:#3c7962;">Mengapa Pilih ReWear?</h2>
    <div class="row mt-5">
        <div class="col-md-4 mb-4" data-aos="zoom-in">
            <div class="feature-box h-100">
                <i class="fas fa-clock feature-icon"></i>
                <h4 class="feature-title">Cepat & Mudah</h4>
                <p>Proses permohonan dan sumbangan yang ringkas dan mesra pengguna.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="feature-box h-100">
                <i class="fas fa-lock feature-icon"></i>
                <h4 class="feature-title">Selamat & Dipercayai</h4>
                <p>Transaksi dilakukan secara terus antara pengguna tanpa maklumat peribadi didedahkan.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="feature-box h-100">
                <i class="fas fa-users feature-icon"></i>
                <h4 class="feature-title">Komuniti Prihatin</h4>
                <p>Memupuk amalan berkongsi dan menyokong komuniti UKM melalui pakaian terpakai.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; <?= date('Y') ?> ReWear UKM. Semua Hak Cipta Terpelihara. 
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
