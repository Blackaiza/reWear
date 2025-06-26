<?php
include 'database.php';

// Fetch tips dari DB
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT * FROM tbl_tipsdiy ORDER BY id DESC");
$stmt->execute();
$tipsList = $stmt->fetchAll();

function getFirstLine($text) {
    // Buang link YouTube
    $text = preg_replace('/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)[^\s]+/', '', $text);

    // Buang tag HTML
    $clean = strip_tags($text);

    // Pecah ikut baris
    $lines = preg_split('/\r\n|\r|\n/', trim($clean));

    // Cuba ambil baris pertama yang ada huruf/nombor
    foreach ($lines as $line) {
        if (trim($line) !== '' && preg_match('/[a-zA-Z0-9]/', $line)) {
            return trim($line);
        }
    }

    // Jika semua kosong atau hanya emoji, ambil sahaja baris pertama yang tak kosong
    foreach ($lines as $line) {
        if (trim($line) !== '') {
            return trim($line);
        }
    }

    return 'Tiada ringkasan.';
}


?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>ReWear: Tips DIY</title>
    <!-- Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
  /* Umum */
  body {
    font-family: 'Poppins', sans-serif;
  }

  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }

  /* Seksyen Hero */
  .hero-section {
    background: url('bg_jahit.jpg') no-repeat center center/cover;
    color: white;
    text-align: center;
    padding: 40px 20px 60px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    position: relative;
  }

  .hero-section h1 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 30px;
  }

  /* Butang Kembali */
  .btn-back {
    position: absolute;
    top: 20px;
    left: 20px;
    background: white;
    color: #3c7962;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .btn-back:hover {
    background-color: #3c7962;
    color: white;
  }

  /* Kad DIY */
  .card-diy {
    background-color: white;
    color: #2c5234;
    border-radius: 20px;
    padding: 20px;
    margin: 10px auto;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    max-width: 300px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .card-diy:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  }

  .card-diy h5 {
    font-weight: 700;
    color: #2c5234;
    margin-bottom: 10px;
    font-size: 16px;
  }

  .card-diy p {
    text-align: left;
    padding-left: 5px;
    margin: 3px 0;
    font-size: 14px;
    color: #444;
  }

  /* Senarai bahan */
  .bahan-item {
    margin: 2px 0;
    padding: 0;
  }

  /* Butang "Lebih Lanjut" */
  .btn-more {
    background-color: #a7c4a2;
    border: none;
    color: #fff;
    font-weight: 500;
    border-radius: 20px;
    padding: 6px 20px;
    font-size: 14px;
    align-self: center;
    margin-top: 12px;
  }
</style>

</head>
<body>

<?php include 'nav_bar_nu.php'; ?>

<!-- Hero -->
<section class="hero-section">
  <a href="upcycle.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
  <h1>Tips DIY</h1>

  <div class="container">
    <div class="row justify-content-center">
      <?php foreach ($tipsList as $tip): ?>
        <div class="col-md-4 d-flex justify-content-center">
        <div class="card-diy">
        <h5><strong><?= htmlspecialchars($tip['tajuk']) ?></strong></h5>
        <p><strong>Bahan Diperlukan:</strong></p>
<?php
$bahanLines = explode("\n", strip_tags(trim($tip['bahan'])));
foreach ($bahanLines as $line) {
    $line = trim($line);
    if ($line !== '') {
        echo "<p class='bahan-item'>" . htmlspecialchars($line) . "</p>";
    }
}
?>

            <a href="diy_detail.php?id=<?= $tip['id'] ?>" class="btn btn-more">Lebih Lanjut</a>
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