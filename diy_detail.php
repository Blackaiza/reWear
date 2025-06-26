<?php
include 'database.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM tbl_tipsdiy WHERE id = ?");
$stmt->execute([$id]);
$tip = $stmt->fetch();

// Tidak perlu pecah kandungan – semua sudah dipisah
// Pastikan data wujud
if (!$tip) {
  die("Tips tidak dijumpai.");
}

?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>ReWear: <?= htmlspecialchars($tip['tajuk']) ?></title>
  <!-- WAJIB untuk dropdown -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    body {
  font-family: 'Poppins', sans-serif;
  background: url('bg_jahit.jpg') no-repeat center center fixed;
  background-size: cover;
}

    .container-detail {
      background-color: white;
      max-width: 800px;
      margin: 40px auto;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    h2 {
      font-weight: 700;
      color: #2c5234;
    }
    iframe {
      margin: 20px 0;
      border-radius: 10px;
    }
    p {
      text-align: left;
      font-size: 16px;
    }
    .langkah-utama {
      font-weight: bold;
      margin-top: 10px;
    }
    .emoji-step {
      background-color: #3c7962;
      color: white;
      padding: 3px 8px;
      border-radius: 5px;
      font-size: 14px;
      margin-right: 8px;
    }
    .bullet {
      color: #3c7962;
      margin-right: 5px;
    }
    .btn-back-top {
  background: white;
  color: #3c7962;
  border: 2px solid #3c7962;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: 0.3s;
}

.btn-back-top:hover {
  background-color: #3c7962;
  color: white;
}

  </style>
</head>
<body>

<!-- Include Navbar -->
<?php include 'nav_bar_nu.php'; ?>

<div class="container-detail">
<div class="d-flex justify-content-start mb-2">
  <a href="nu_tipsdiy.php" class="btn-back-top">
    <i class="fas fa-arrow-left"></i>
  </a>
</div>
  <h2><?= htmlspecialchars($tip['tajuk']) ?> 👜 ♻️</h2>

 <!-- YouTube Video -->
<?php if (!empty($tip['youtube'])): ?>
  <iframe width="100%" height="350" src="https://www.youtube.com/embed/<?= htmlspecialchars($tip['youtube']) ?>" 
          frameborder="0" allowfullscreen></iframe>
<?php endif; ?>

<!-- Bahan -->
<h5 class='mt-4'><strong>Bahan Diperlukan:</strong></h5>
<?php
$bahanLines = explode("\n", $tip['bahan']);
foreach ($bahanLines as $line) {
    $line = trim($line);
    if ($line !== '') {
        echo "<p>" . htmlspecialchars($line) . "</p>";
    }
}
?>

<!-- Langkah -->
<h5 class='mt-4'><strong>Langkah – Langkah:</strong></h5>
<?php
$langkahLines = explode("\n", $tip['langkah']);
$stepNo = 1;
foreach ($langkahLines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '') continue;

    if (preg_match('/^\d+[\.\)]/', $trimmed) || str_contains($trimmed, 'Beg')) {
        echo "<p><span class='emoji-step'>{$stepNo}</span> <span class='langkah-utama'>" . htmlspecialchars($trimmed) . "</span></p>";
        $stepNo++;
    } else {
        echo "<p><span class='bullet'>◆</span> " . htmlspecialchars($trimmed) . "</p>";
    }
}
?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
