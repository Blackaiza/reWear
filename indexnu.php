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
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ReWear: Utama</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">


<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        scroll-behavior: smooth;
    }
    .active-section {
        border-bottom: 3px solid #3c7962 !important;
    }
    .hero-section {
        position: relative;
        display: grid;
        place-items: center;
        text-align: center;
        color: white;
        overflow: hidden;
        height: 80vh;
        padding: 2vh 2rem;
    }
    .transition-images {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: -1;
        animation: fadeBackground 15s infinite;
        background-size: cover;
        background-position: center;
        transition: background-image 1s ease-in-out;
    }
    @keyframes fadeBackground {
        0% {background-image: url('bg2.jpg');}
        25% {background-image: url('bg2.jpg');}
        50% {background-image: url('bg.jpg');}
        75% {background-image: url('bg2.jpg');}
        100% {background-image: url('dashboard1.jpg');}
    }
    .hero-section h1, .hero-section p, .cta-btn {
        opacity: 0;
        transform: translateY(30px);
    }
    .hero-section h1 {
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        animation: fadeInUp 1.5s forwards 0.5s;
    }
    .hero-section p {
        font-size: 1.25rem;
        margin-bottom: 25px;
        font-weight: 400;
        animation: fadeInUp 1.5s forwards 1s;
    }
    .cta-btn {
        background: #fff;
        color: #000;
        padding: 12px 40px;
        font-weight: 600;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        animation: fadeScaleUp 1.5s forwards 1.5s;
    }
    .cta-btn:hover {
        background: #f1f1f1;
        transform: scale(1.05);
    }
    @keyframes fadeInUp {
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeScaleUp {
        to { opacity: 1; transform: scale(1); }
    }
    .feature-section {
        background: linear-gradient(135deg, rgb(255, 247, 240) 0%, rgb(255, 255, 255) 100%);
        padding: 100px 0;
    }
    .feature-box {
        background: #ffffff;
        border: none;
        border-radius: 25px;
        padding: 50px 35px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        transition: 0.4s ease;
        text-align: center;
        position: relative;
    }
    .feature-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 60px rgba(0,0,0,0.15);
    }
    .feature-icon-wrapper {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #3c7962 0%, #60a38a 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px auto;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        animation: float 3s ease-in-out infinite;
    }
    .feature-box:hover .feature-icon-wrapper {
        background: linear-gradient(135deg, #2f5d4c 0%, #3c7962 100%);
        transform: scale(1.1);
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    .feature-icon {
        font-size: 3rem;
        color: #fff;
    }
    .feature-title {
        font-weight: 700;
        margin-top: 10px;
        font-size: 1.5rem;
        color: #3c7962;
    }
    .about-section {
        background: linear-gradient(135deg, rgb(173, 223, 204) 0%, rgb(245, 245, 245) 100%);
        padding: 100px 0;
        transition: background 1s ease;
    }
    .about-section p {
        font-size: 1.2rem;
        color: #333;
    }
    footer {
        background: #3c7962;
        color: #fff;
        text-align: center;
        padding: 20px 0;
        font-weight: 500;
    }
    @media (max-width: 768px) {
        .hero-section { height: 100vh; padding: 20vh 2rem; }
        .hero-section h1 { font-size: 2.5rem; }
        .hero-section p { font-size: 1.1rem; }
        .cta-btn { font-size: 1rem; padding: 10px 30px; }
    }
</style>
</head>
<body>

<?php include 'nav_bar_nu.php'; ?>

<section class="hero-section" id="home">
    <div class="transition-images"></div>
    <div class="container">
        <h1>Selamat Datang ke ReWear</h1>
        <p>Platform pertukaran dan pemberian pakaian terpakai untuk komuniti UKM.</p>
        <a href="muatnaik_form.php" class="cta-btn">+ Muatnaik Pakaian</a>
    </div>
</section>

<!-- Bahagian CIR-CIRI KAMI -->
<section id="features" class="feature-section">
  <div class="container">
    <h2 class="text-center fw-bold mb-5" style="color:#3c7962;" data-aos="fade-up">Ciri-ciri Kami</h2>
    <div class="row g-5">
      <div class="col-md-4" data-aos="fade-up">
        <div class="feature-box h-100">
          <div class="feature-icon-wrapper">
            <i class="fas fa-money-bill-wave feature-icon"></i>
          </div>
          <h4 class="feature-title">Tiada bayaran dikenakan</h4>
          <p>Semua transaksi dalam Rewear adalah percuma.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
        <div class="feature-box h-100">
          <div class="feature-icon-wrapper">
            <i class="fas fa-gift feature-icon"></i>
          </div>
          <h4 class="feature-title">Platform Pertukaran</h4>
          <p>Pengguna boleh menukar pakaian dalam kalangan komuniti UKM.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="feature-box h-100">
          <div class="feature-icon-wrapper">
            <i class="fas fa-comments feature-icon"></i>
          </div>
          <h4 class="feature-title">Mesej Secara Langsung</h4>
          <p>Berhubung terus dengan pemberi atau pemohon untuk urusan penyerahan.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="about" class="about-section">
    <div class="container">
        <h2 class="text-center fw-bold mb-4" style="color:#3c7962;" data-aos="fade-up">"Tiada Baju Last Minute? Gunakan Rewear!"</h2>
        <p class="text-center" data-aos="fade-up" data-aos-delay="200">
            ReWear adalah solusi mudah untuk anda yang memerlukan pakaian dengan segera. Dengan pelbagai pilihan pakaian yang tersedia untuk diberi atau ditukar.
        </p>
    </div>
</section>

<!-- Bahagian MENGAPA PILIH REWEAR -->
<section id="benefits" class="container py-5">
  <h2 class="text-center fw-bold" style="color:#3c7962;" data-aos="fade-up">Mengapa Pilih ReWear?</h2>
  <div class="row mt-5">
    <div class="col-md-4 mb-4" data-aos="zoom-in">
      <div class="feature-box h-100">
        <div class="feature-icon-wrapper">
          <i class="fas fa-clock feature-icon"></i>
        </div>
        <h4 class="feature-title">Cepat & Mudah</h4>
        <p>Platform yang mesra pengguna dan direka untuk memenuhi keperluan pengguna dengan cepat dan mudah.</p>
      </div>
    </div>
    <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="150">
      <div class="feature-box h-100">
        <div class="feature-icon-wrapper">
          <i class="fas fa-wallet feature-icon"></i>
        </div>
        <h4 class="feature-title">Menjimatkan Wang</h4>
        <p>Dapatkan pakaian secara percuma dan jimatkan kos untuk membeli pakaian baru.</p>
      </div>
    </div>
    <div class="col-md-4 mb-4" data-aos="zoom-in" data-aos-delay="300">
      <div class="feature-box h-100">
        <div class="feature-icon-wrapper">
          <i class="fas fa-users feature-icon"></i>
        </div>
        <h4 class="feature-title">Mengurangkan pembaziran pakaian</h4>
        <p>Menyokong amalan berkongsi dan membantu mengurangkan pembaziran pakaian.</p>
      </div>
    </div>
  </div>
</section>

<script>
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll("section");
    const scrollPos = window.scrollY + 200;
    sections.forEach(sec => {
        if (scrollPos > sec.offsetTop && scrollPos < sec.offsetTop + sec.offsetHeight) {
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove("active-section");
                if (link.getAttribute("href") === '#' + sec.id) {
                    link.classList.add("active-section");
                }
            });
        }
    });
});
</script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 700,
    once: false
  });
</script>

<footer>
  &copy; 2025 ReWear. Semua Hak Cipta Terpelihara.
</footer>

</body>
</html>