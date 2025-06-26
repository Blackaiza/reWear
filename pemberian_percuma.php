<?php
session_start();
include 'database.php';
require 'mailer_config.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$noMatrik = $_SESSION['no_matrik'];
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idPakaian'])) {
        $idPakaian = $_POST['idPakaian'];
        $matrikPeminta = $_SESSION['no_matrik'];

        $check = $conn->prepare("SELECT * FROM tbl_percuma WHERE idPakaian = ? AND matrikPeminta = ? ORDER BY idPercuma DESC LIMIT 1");
        $check->execute([$idPakaian, $matrikPeminta]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);

        if (!$existing || ($existing['status'] === 'Ditolak')) {
            $stmt = $conn->prepare("SELECT noMatrik, namaPakaian, gambar, saiz FROM tbl_pakaian WHERE idPakaian = ?");
            $stmt->execute([$idPakaian]);
            $dataPakaian = $stmt->fetch(PDO::FETCH_ASSOC);
            $noMatrikPemilik = $dataPakaian['noMatrik'];

            if (!$noMatrikPemilik) {
                echo "<script>alert('Maklumat pemilik tidak dijumpai.'); window.history.back();</script>";
                exit();
            }

            $tarikh = date('Y-m-d H:i:s');
            $insert = $conn->prepare("INSERT INTO tbl_percuma (idPakaian, matrikPeminta, noMatrikPemilik, tarikh_minta, status) VALUES (?, ?, ?, ?, 'Menunggu')");
            $insert->execute([$idPakaian, $matrikPeminta, $noMatrikPemilik, $tarikh]);

            $stmt = $conn->prepare("SELECT emel, nama FROM tbl_pengguna WHERE noMatrik = ?");
            $stmt->execute([$noMatrikPemilik]);
            $pemilik = $stmt->fetch(PDO::FETCH_ASSOC);
            $emailPemilik = $pemilik['emel'];
            $namaPemilik = $pemilik['nama'];

            $mail->addAddress($emailPemilik, $namaPemilik);
            $mail->Subject = "Permintaan Baharu untuk Pakaian Anda di ReWear";
            $mail->isHTML(true);

            $gambarUtama = explode(',', $dataPakaian['gambar'])[0];
            if (!str_starts_with($gambarUtama, 'uploads/')) {
                $gambarUtama = 'uploads/' . $gambarUtama;
            }
            if (file_exists($gambarUtama)) {
                $mail->addEmbeddedImage($gambarUtama, 'pakaianImage');
            }

            $mail->Body = '
            <div style="font-family:Poppins,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;border-radius:10px">
                <div style="background:#3c7962;color:white;padding:15px;text-align:center;border-radius:10px 10px 0 0;">
                    <h2 style="margin:0;">Permintaan Pakaian Percuma</h2>
                </div>
                <div style="background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;">
                    <p>Hai <b>' . $namaPemilik . '</b>,</p>
                    <p>Anda telah menerima permintaan pakaian percuma melalui sistem <b>ReWear</b>.</p>
                    <div style="border:1px solid #ccc;padding:20px;border-radius:8px;margin:20px 0;">
                        <h4 style="margin-top:0;">Maklumat Permintaan:</h4>
                        <p><b>Pemohon:</b> ' . $matrikPeminta . '</p>
                        <p><b>Tarikh Permintaan:</b> ' . date('d-m-Y H:i') . '</p>
                        <p><b>Pakaian Anda:</b> ' . $dataPakaian['namaPakaian'] . ' (Saiz: ' . $dataPakaian['saiz'] . ')<br><img src="cid:pakaianImage" width="150"></p>
                        <p style="margin-top:25px;">Sila log masuk ke sistem ReWear untuk menguruskan permintaan ini.</p>
                        <div style="margin-top:20px;text-align:center;">
                            <a href="https://amalia.aizathami.website/pemberian_percuma.php?tab=permohonan" style="background:#3c7962;color:white;padding:12px 25px;border-radius:50px;text-decoration:none;font-weight:bold;font-size:16px;">Semak Permohonan</a>
                        </div>
                    </div>
                    <p style="font-size:12px;color:#999;text-align:center;margin-top:30px;">Email ini dihantar secara automatik. Sila jangan balas email ini.</p>
                </div>
            </div>';
            $mail->send();
        }
        header("Location: pemberian_percuma.php?tab=permintaan");
        exit();
    }

    if (isset($_POST['batalkan_id'])) {
        $idBatalkan = $_POST['batalkan_id'];
        $delete = $conn->prepare("DELETE FROM tbl_percuma WHERE idPercuma = ? AND matrikPeminta = ? AND status = 'Menunggu'");
        $delete->execute([$idBatalkan, $noMatrik]);
        header("Location: pemberian_percuma.php?tab=permintaan");
        exit();
    }
}

$stmt1 = $conn->prepare("SELECT pp.*, p.namaPakaian FROM tbl_percuma pp JOIN tbl_pakaian p ON pp.idPakaian = p.idPakaian WHERE pp.matrikPeminta = ? ORDER BY pp.tarikh_minta DESC");
$stmt1->execute([$noMatrik]);
$permintaanSaya = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $conn->prepare("SELECT pp.*, p.namaPakaian, u.nama FROM tbl_percuma pp JOIN tbl_pakaian p ON pp.idPakaian = p.idPakaian JOIN tbl_pengguna u ON pp.matrikPeminta = u.noMatrik WHERE p.noMatrik = ? ORDER BY pp.tarikh_minta DESC");
$stmt2->execute([$noMatrik]);
$permohonanMasuk = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$countPermohonan = count(array_filter($permohonanMasuk, function($item) {
    return $item['status'] === 'Menunggu';
}));

$activeTab = $_GET['tab'] ?? 'permintaan';
date_default_timezone_set('Asia/Kuala_Lumpur');
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Pakaian Percuma</title>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color:rgb(255, 255, 255);
    }
    .container-card {
      background-color: #fff;
      border-radius: 15px;
      padding: 40px;
      max-width: 1100px;
      margin: 50px auto;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    h3.title {
      text-align: center;
      font-weight: 700;
      color: #3c7962;
      margin-bottom: 30px;
      font-size: 32px;
    }
    .nav-tabs .nav-link.active {
      background-color: #3c7962 !important;
      color: white !important;
      border-radius: 50px;
      font-weight: 500;
    }
    .nav-tabs .nav-link {
      border: none;
      color: #3c7962;
      font-weight: 500;
    }
    .badge {
      font-size: 13px;
      padding: 8px 16px;
      border-radius: 50px;
      font-weight: 500;
    }
    table {
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .table th, .table td {
      vertical-align: middle !important;
    }
    .btn {
      border-radius: 30px;
    }
    .btn:hover {
      opacity: 0.85;
    }
    .badge-warning {
      background-color: #ffe39c !important;
      color: #856404 !important;
    }
    .badge-success {
      background-color: #7dd87d !important;
      color: #fff !important;
    }
    .badge-danger {
      background-color: #e06666 !important;
      color: #fff !important;
    }
    td:last-child {
    text-align: center; /* atau 'left' kalau nak kiri */
    vertical-align: middle;
  }

.btn-mesej {
  background-color: #3c7962;
  color: #fff;
  border: none;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-decoration: none; /* buang underline */
}

.btn-mesej:hover {
  background-color: #2e5e4d; /* warna hover gelap sikit dari asal */
  color: #fff; /* kekal putih bila hover */
  text-decoration: none; /* elak underline */
}


  </style>
</head>
<body>
<?php include 'nav_bar_nu.php'; ?>

<div class="container-card">
<h3 class="title">PAKAIAN PERCUMA</h3>
<ul class="nav nav-tabs mb-4 justify-content-center">
  <li class="nav-item">
    <a class="nav-link <?= $activeTab === 'permintaan' ? 'active' : '' ?>" href="?tab=permintaan">Permintaan Saya</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?= $activeTab === 'permohonan' ? 'active' : '' ?>" href="?tab=permohonan">
      Permintaan Masuk <?php if ($countPermohonan > 0): ?><span class="badge bg-primary ms-1"><?= $countPermohonan ?></span><?php endif; ?>
    </a>
  </li>
</ul>

<?php if ($activeTab === 'permintaan'): ?>
  <?php if (empty($permintaanSaya)): ?>
    <div class="alert alert-secondary text-center">Tiada Permintaan Dibuat</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr><th>Nama Pakaian</th><th>Tarikh Permintaan</th><th>Status</th><th>Butiran</th><th style="text-align: center;">Tindakan</th></tr>
        </thead>
        <tbody>
          <?php foreach ($permintaanSaya as $row): 
            $class = "badge ";
            if ($row['status'] == 'Menunggu') $class .= "badge-warning";
            elseif ($row['status'] == 'Diterima') $class .= "badge-success";
            elseif ($row['status'] == 'Ditolak') $class .= "badge-danger";
          ?>
          <tr>
            <td><?= htmlspecialchars($row['namaPakaian']) ?></td>
            <td><?= (new DateTime($row['tarikh_minta']))->format('d-m-Y H:i') ?></td>
            <td><span class="<?= $class ?>"><?= $row['status'] ?></span></td>
            <td><a href="detail_percuma.php?id=<?= $row['idPercuma'] ?>" class="btn btn-outline-secondary btn-sm">Lihat</a></td>
            <td>
              <?php if ($row['status'] == 'Menunggu'): ?>
                <button class="btn btn-danger btn-sm batal-btn" data-id="<?= $row['idPercuma'] ?>">Batalkan</button>
              <?php elseif ($row['status'] == 'Diterima'): ?>
              <a href="paparan_mesej.php?chat_with=<?= $row['noMatrikPemilik'] ?>&idPakaian=<?= $row['idPakaian'] ?>" class="btn-mesej">
              <i class="fas fa-envelope"></i> Mesej Pemilik
                </a>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php elseif ($activeTab === 'permohonan'): ?>
  <?php if (empty($permohonanMasuk)): ?>
    <div class="alert alert-secondary text-center">Tiada permohonan diterima ke atas pakaian anda.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr><th>Pemohon</th><th>Nama Pakaian</th><th>Tarikh</th><th>Status</th><th>Tindakan</th><th>Butiran</th></tr>
        </thead>
        <tbody>
          <?php foreach ($permohonanMasuk as $row): 
            $class = "badge ";
            if ($row['status'] == 'Menunggu') $class .= "badge-warning";
            elseif ($row['status'] == 'Diterima') $class .= "badge-success";
            elseif ($row['status'] == 'Ditolak') $class .= "badge-danger";
          ?>
          <tr>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['namaPakaian']) ?></td>
            <td><?= (new DateTime($row['tarikh_minta']))->format('d-m-Y H:i') ?></td>
            <td><span class="<?= $class ?>"><?= $row['status'] ?></span></td>
            <td>
              <?php if ($row['status'] == 'Menunggu'): ?>
                <button type="button" class="btn btn-success btn-sm" onclick="confirmAction('Diterima', <?= $row['idPercuma'] ?>)">✔ Terima</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('Ditolak', <?= $row['idPercuma'] ?>)">✖ Tolak</button>
              <?php else: ?>
                <span class="text-muted">Telah <?= strtolower($row['status']) ?></span>
              <?php endif; ?>
            </td>
            <td><a href="detail_percuma.php?id=<?= $row['idPercuma'] ?>" class="btn btn-outline-secondary btn-sm">Lihat</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
<?php endif; ?>
</div>

<script>
$(document).ready(function() {
  $('.batal-btn').click(function() {
    var id = $(this).data('id');
    Swal.fire({
      title: 'Anda pasti?',
      text: "Permintaan akan dibatalkan.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3c7962',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, Batalkan'
    }).then((result) => {
      if (result.isConfirmed) {
        $('<form method="POST"><input type="hidden" name="batalkan_id" value="' + id + '"></form>').appendTo('body').submit();
      }
    });
  });
});

function confirmAction(status, id) {
  Swal.fire({
    title: 'Anda pasti?',
    text: "Permohonan ini akan " + status.toUpperCase() + ".",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3c7962',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, teruskan!'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'kemaskini_percuma.php';
      form.innerHTML = `<input type="hidden" name="idPermohonan" value="${id}">` +
                        `<input type="hidden" name="status" value="${status}">`;
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>

</body>
</html>
