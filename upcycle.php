<?php
session_start();
include 'database.php';
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
  <title>ReWear: Apa Itu Upcycle</title>
  <!-- Fonts & CSS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
    }

    .hero-section {
      background: url('upcycle.jpg') no-repeat center center/cover;
      height: 50vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      position: relative;
      padding-bottom: 20px;
    }

    .hero-section h1 {
      font-size: 48px;
      font-weight: 700;
      z-index: 2;
    }

    .description-section {
      padding: 40px 20px;
      background-color: #fff;
      text-align: center;
    }

    .description-section p {
      font-size: 18px;
      max-width: 800px;
      margin: 0 auto 40px;
      line-height: 1.7;
      text-align: justify;
      color: #333;
    }

    .hero-buttons a {
      margin: 10px;
      padding: 12px 24px;
      border-radius: 25px;
      background-color: #2c5234;
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      border: 2px solid transparent;
      transition: all 0.3s ease-in-out;
    }

    .hero-buttons a:hover {
      background-color: transparent;
      border: 2px solid #2c5234;
      color: #2c5234;
    }
    .btn-custom {
  margin: 5px 10px 0;
  padding: 12px 24px;
  border-radius: 25px;
  background-color:rgb(78, 118, 87);
  color: #fff;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  border: 2px solid transparent;
  transition: all 0.3s ease-in-out;
}

.btn-custom:hover {
  background-color: transparent;
  border: 2px solid #2c5234;
  color: #2c5234;
}

  </style>
</head>
<body>

<?php include 'nav_bar_nu.php'; ?>

<!-- SECTION 1: Hanya Tajuk -->
<section class="hero-section">
  <h1 data-aos="fade-down" data-aos-duration="1000">APA ITU UPCYCLE ?</h1>
</section>

<!-- SECTION 2: Huraian + Butang (Scroll Muncul) -->
<section class="description-section">
  <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
    <p>
      Upcycle ialah proses menukar barang atau bahan terpakai kepada produk baharu yang berkualiti lebih tinggi atau bernilai estetika yang lebih baik daripada keadaan asalnya. Berbeza dengan kitar semula yang memproses bahan untuk digunakan semula, upcycling lebih memfokuskan kepada kreativiti dan inovasi untuk memberikan fungsi baharu kepada barang yang dianggap tidak berguna.
    </p>
    <div class="mt-3">
      <a href="nu_tipsdiy.php" class="btn-custom">Tips DIY</a>
      <a href="nu_tukangjahit.php" class="btn-custom">Senarai Tukang Jahit</a>
    </div>
  </div>
</section>


<!-- Script AOS & JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ once: true });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
