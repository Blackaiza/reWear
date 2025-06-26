<?php
session_start();
include 'database.php';

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM tbl_pakaian WHERE idPakaian = :id");
    $stmt->bindParam(':id', $itemId);
    $stmt->execute();
    $item = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ReWear: Detail Pakaian</title>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body { font-family: 'Poppins', sans-serif; background: url('dashboard1.jpg') center fixed; color: #333; }
    .container { margin-top: 40px; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); }
    .title-bold { font-weight: 700; text-align: center; color: #3c7962; margin-bottom: 20px; }
    .item-image { padding: 10px; border-radius: 10px; }
    .item-details { background-color: #fff; padding: 20px; border-radius: 10px; margin-top: 20px; }
    .item-details th { width: 30%; text-align: left; font-weight: bold; color: #3a5129; }
    .item-details td { font-weight: 400; color: #555; }
    .message-btn { margin-top: 20px; background-color: #3c7962; color: white; border-radius: 25px; padding: 10px 20px; text-transform: uppercase; font-weight: 600; }
    .message-btn:hover { background-color: #2C4230; }
    .row { display: flex; justify-content: center; align-items: center; }
    .col-md-6 { padding: 15px; }
    .back-btn { margin-bottom: 20px; font-size: 1rem; color: #3c7962; text-decoration: none; }
    .back-btn:hover { color: #2C4230; }
.btn-custom {
  background-color: #3c7962;
  color: white;
  border: none;
  border-radius: 25px;
  padding: 10px 20px;
  font-weight: 500;
  transition: background-color 0.3s;
  text-decoration: none;
  display: block; /* Tukar ke block supaya margin auto berfungsi */
  margin: 0 auto;
    }

    .btn-custom:hover {
      background-color: #2C4230;
      color: #fff;
    }
</style>
</head>
<body>
<?php include 'nav_bar_nu.php'; ?>
<div class="container">
<a href="katalog.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
<div class="row">
<div class="col-md-6 item-image">
<?php $gambarArray = explode(',', $item['gambar']); ?>
<div class="text-center mb-3">
<img id="mainImage" src="<?php echo $gambarArray[0]; ?>" class="img-fluid rounded" style="width: 300px; height: 400px; object-fit: cover; background-color: #f8f8f8;" alt="Gambar Utama">
</div>
<div class="d-flex flex-wrap justify-content-center gap-2">
<?php foreach ($gambarArray as $gambar): ?>
<img src="<?php echo $gambar; ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" onclick="tukarGambar('<?php echo $gambar; ?>')">
<?php endforeach; ?>
</div>
<script>function tukarGambar(src) { document.getElementById('mainImage').src = src; }</script>
</div>
<div class="col-md-6 item-details">
<h2 class="title-bold">Maklumat Pakaian</h2>
<table class="table table-bordered">
<tr><th>Nama</th><td><?php echo $item['namaPakaian']; ?></td></tr>
<tr><th>Jenama</th><td><?php echo $item['jenama']; ?></td></tr>
<tr><th>Saiz</th><td><?php echo $item['saiz']; ?></td></tr>
<tr><th>Kategori</th><td><?php echo $item['kategori']; ?></td></tr>
<tr><th>Deskripsi</th><td><?php echo $item['deskripsi']; ?></td></tr>
<tr><th>Jenis Pemberian</th><td><?php echo $item['jenis_pemberian']; ?></td></tr>
</table>
<?php if ($item['noMatrik'] !== $_SESSION['no_matrik']): ?>
<?php if (strtolower($item['jenis_pemberian']) === 'pertukaran'): ?>
<form action="borang_pertukaran.php" method="GET" class="mt-3">
    <input type="hidden" name="idPakaian" value="<?= $item['idPakaian'] ?>">
    <button type="submit" class="btn-custom">Pilih Pakaian untuk Ditukar</button>
</form>
<?php elseif (strtolower($item['jenis_pemberian']) === 'percuma'): ?>
<form id="pemberianForm" action="pemberian_percuma.php" method="POST" class="mt-3">
    <input type="hidden" name="idPakaian" value="<?= $item['idPakaian']; ?>">
    <input type="hidden" name="idPemilik" value="<?= $item['noMatrik']; ?>">
    <button type="button" id="submitBtn" class="btn-custom">Saya Inginkan Baju Ini</button>
</form>
<?php endif; ?>

<?php endif; ?>
</div>
</div>
</div>
<script>
$(document).ready(function() {
  $('#submitBtn').click(function() {
    Swal.fire({
      title: 'Adakah anda pasti?',
      text: "Permintaan akan dihantar kepada pemilik.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3c7962',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, Hantar'
    }).then((result) => {
      if (result.isConfirmed) {
        $('#pemberianForm').submit();
      }
    });
  });
});
</script>
</body>
</html>
