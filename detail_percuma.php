<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Permintaan tidak sah.";
    exit();
}

$id = $_GET['id'];
$noMatrik = $_SESSION['no_matrik'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("
    SELECT p.*, 
           k.namaPakaian, k.saiz, k.jenama, k.kategori, k.deskripsi, k.gambar, k.noMatrik AS noMatrikPemilik,
           u.nama AS namaPeminta
    FROM tbl_percuma p
    JOIN tbl_pakaian k ON p.idPakaian = k.idPakaian
    JOIN tbl_pengguna u ON p.matrikPeminta = u.noMatrik
    WHERE p.idPercuma = :id AND (:noMatrik = p.matrikPeminta OR :noMatrik = k.noMatrik)
");

    $stmt->execute([
        ':id' => $id,
        ':noMatrik' => $noMatrik
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Permohonan tidak dijumpai atau anda tidak mempunyai akses.";
        exit();
    }
} catch (PDOException $e) {
    echo "Ralat: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Butiran Permohonan Percuma</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fff;
      color: #333;
    }

    .container-detail {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

  .img-thumb {
      width: 100%;
      height: auto;
      max-height: 250px;
      object-fit: contain;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    h4 {
      color: #3c7962;
      font-weight: 600;
      margin-bottom: 25px;
      text-align: center;
    }

    .btn-custom {
      background-color: #3c7962;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 10px 20px;
      font-weight: 500;
      text-transform: uppercase;
      transition: background-color 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-custom:hover {
      background-color: #2C4230;
      color: #fff;
    }

    .btn-back {
      margin-top: 30px;
      background-color: transparent;
      border: 1px solid #3c7962;
      color: #3c7962;
      border-radius: 25px;
      padding: 8px 20px;
      transition: 0.3s;
      text-decoration: none;
    }

    .btn-back:hover {
      background-color: #3c7962;
      color: white;
    }

    p {
      margin-bottom: 10px;
      font-size: 1rem;
    }

    .badge {
      padding: 0.5em 1em;
      font-size: 0.9em;
      border-radius: 20px;
    }
    .btn-container {
    margin-top: 20px;
    text-align: left;
  }
   @media (max-width: 768px) {
    .btn-container {
      text-align: center;
    }
  }
  </style>
</head>
<body>

<?php include 'nav_bar_nu.php'; ?>

<div class="container-detail">
  <h4 class="mb-4">Butiran Permohonan Pakaian Percuma</h4>

  <div class="row">
    <div class="col-md-5">
      <?php $gambarUtama = explode(',', $row['gambar'])[0]; ?>
      <img src="<?= htmlspecialchars($gambarUtama) ?>" class="img-thumb img-fluid" alt="Pakaian">
    </div>
    <div class="col-md-7">
      <p><strong>Nama:</strong> <?= htmlspecialchars($row['namaPakaian']) ?></p>
      <p><strong>Saiz:</strong> <?= htmlspecialchars($row['saiz']) ?></p>
      <p><strong>Jenama:</strong> <?= htmlspecialchars($row['jenama']) ?></p>
      <p><strong>Kategori:</strong> <?= htmlspecialchars($row['kategori']) ?></p>
      <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
    </div>
  </div>

  <hr>

  <?php
  
  $dt = new DateTime($row['tarikh_minta'], new DateTimeZone('UTC'));
  $dt->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));
  $tarikhMalaysia = $dt->format('d-m-Y H:i');
  ?>
  <p><strong>Pemohon:</strong> <?= htmlspecialchars($row['namaPeminta']) ?> (<?= $row['matrikPeminta'] ?>)</p>
  <p><strong>Tarikh Permintaan:</strong> <?= $tarikhMalaysia ?></p>
  <p><strong>Status:</strong>
    <?php
    $badgeClass = 'badge ';
    if ($row['status'] == 'Diterima') $badgeClass .= 'bg-success';
    elseif ($row['status'] == 'Ditolak') $badgeClass .= 'bg-danger';
    else $badgeClass .= 'bg-warning text-dark';
    ?>
    <span class="<?= $badgeClass ?>"><?= $row['status'] ?></span>
  </p>

<?php if ($row['status'] === 'Diterima'): ?>
  <div class="btn-container">
    <?php if ($noMatrik === $row['noMatrikPemilik']): ?>
      <a href="paparan_mesej.php?chat_with=<?= $row['matrikPeminta'] ?>&idPakaian=<?= $row['idPakaian'] ?>" class="btn-custom">💬 Mesej Pemohon</a>
    <?php elseif ($noMatrik === $row['matrikPeminta']): ?>
      <a href="paparan_mesej.php?chat_with=<?= $row['noMatrikPemilik'] ?>&idPakaian=<?= $row['idPakaian'] ?>" class="btn-custom">💬 Mesej Pemilik</a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<a href="javascript:history.back()" class="btn-back" style="margin-top: 30px; display: inline-block;">← Kembali</a>


</div>

</body>
</html>
