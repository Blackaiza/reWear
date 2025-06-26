<?php
session_start();
include 'database.php';

// Fetch tukang jahit dari DB
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT * FROM tbl_tukangjahit ORDER BY id DESC");
$stmt->execute();
$tukangList = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>ReWear: Senarai Tukang Jahit</title>
    <!-- Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
.hero-section {
  background: url('bg_jahit.jpg') no-repeat center center/cover;
  color: white;
  text-align: center;
  height: 100vh; /* tinggi satu viewport penuh */
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.hero-section h1 {
  font-size: 48px;
  font-weight: 700;
  margin: 0;
  position: absolute;
  top: 30px; /* jarak dari atas */
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
}

    .card-custom {
      background-color: white;
      border-radius: 20px;
      padding: 20px;
      margin: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      max-width: 300px;
    }
    .card-custom h5 {
      font-weight: 600;
      color: #2c5234;
    }
    .card-custom p {
      margin: 5px 0;
      color: #444;
    }
    .card-custom .phone {
      font-size: 14px;
      color: #c96b00;
      font-weight: 500;
    }
    .btn-back {
    position: absolute;
    top: 20px;
    left: 30px;
    font-size: 20px;
    color: #3c7962;
    text-decoration: none;
    background-color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #3c7962;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s;
    }

    .btn-back:hover {
    background-color: #3c7962;
    color: white;
    }
    .hero-section {
    position: relative;
    }
  </style>
</head>
<body>

<?php include 'nav_bar_nu.php'; ?>

<!-- Hero / Header -->
<section class="hero-section">
<a href="upcycle.php" class="btn-back" title="Kembali ke Upcycle">
  <i class="fas fa-arrow-left"></i>
</a>

  <h1>SENARAI TUKANG JAHIT</h1>

  <!-- Cards Grid -->
  <div class="container">
    <div class="row justify-content-center">
      <?php foreach ($tukangList as $row): ?>
        <div class="col-md-4 d-flex justify-content-center">
          <div class="card-custom text-center">
            <h5><?= htmlspecialchars($row['nama']) ?></h5>
            <p><?= htmlspecialchars($row['alamat']) ?></p>
            <div class="phone"><?= htmlspecialchars($row['telefon']) ?></div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</section>
<!-- Bootstrap Bundle JS + Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
