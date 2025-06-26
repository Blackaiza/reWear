<?php
session_start();
include 'database.php';
require 'mailer_config.php'; // Untuk email config PHPMailer

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['idPakaian'])) {
    $idPakaian = $_GET['idPakaian'];

    $stmt = $conn->prepare("SELECT * FROM tbl_pakaian WHERE idPakaian = :id");
    $stmt->bindParam(':id', $idPakaian);
    $stmt->execute();
    $item = $stmt->fetch();

    $gambarArray = explode(',', $item['gambar']);
    $gambarUtama = trim($gambarArray[0]);
    if (empty($gambarUtama)) {
        $gambarUtama = 'no_image.jpg';
    } elseif (!str_starts_with($gambarUtama, 'uploads/')) {
        $gambarUtama = 'uploads/' . $gambarUtama;
    }
}

$userItems = [];
$stmt = $conn->prepare("SELECT * FROM tbl_pakaian WHERE noMatrik = :noMatrik AND status = 'Tersedia'");
$stmt->bindParam(':noMatrik', $_SESSION['no_matrik']);
$stmt->execute();
$userItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTarget = $_POST['idPakaianTarget'];
    $idTukar = $_POST['pakaian_tukar'];
    $nota = $_POST['nota'];

    if ($idTarget == $idTukar) {
        echo "<script>alert('Anda tidak boleh menukar pakaian dengan item yang sama.'); window.history.back();</script>";
        exit();
    }

    $noMatrikPeminta = $_SESSION['no_matrik'];
    $noMatrikPemilik = $item['noMatrik'];

    $stmt = $conn->prepare("INSERT INTO tbl_pertukaran (idPakaianTarget, idPakaianTukar, noMatrikPeminta, noMatrikPemilik, nota) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$idTarget, $idTukar, $noMatrikPeminta, $noMatrikPemilik, $nota]);

    $stmt = $conn->prepare("SELECT emel, nama FROM tbl_pengguna WHERE noMatrik = ?");
    $stmt->execute([$noMatrikPemilik]);
    $pemilik = $stmt->fetch();

    $stmt = $conn->prepare("SELECT nama, noMatrik FROM tbl_pengguna WHERE noMatrik = ?");
    $stmt->execute([$noMatrikPeminta]);
    $peminta = $stmt->fetch();

    $stmt = $conn->prepare("SELECT namaPakaian, saiz, gambar FROM tbl_pakaian WHERE idPakaian = ?");
    $stmt->execute([$idTukar]);
    $pakaianTukar = $stmt->fetch();

    try {
        $mail->addAddress($pemilik['emel'], $pemilik['nama']);
        $mail->Subject = 'Permintaan Pertukaran Baru Diterima';
        $mail->isHTML(true);

        // Embed gambar pakaian target
        if (file_exists($gambarUtama)) {
            $mail->addEmbeddedImage($gambarUtama, 'pakaianTarget');
        }

        // Embed gambar pakaian tukar
        $gambarTukarPath = !empty($pakaianTukar['gambar']) ? (str_starts_with($pakaianTukar['gambar'], 'uploads/') ? $pakaianTukar['gambar'] : 'uploads/'.$pakaianTukar['gambar']) : 'no_image.jpg';
        if (file_exists($gambarTukarPath)) {
            $mail->addEmbeddedImage($gambarTukarPath, 'pakaianTukar');
        }

        $mail->Body = '
        <div style="font-family:Poppins,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;border-radius:10px">
            <div style="background:#3c7962;color:white;padding:15px;text-align:center;border-radius:10px 10px 0 0;">
                <h2 style="margin:0;">Permintaan Pertukaran Pakaian</h2>
            </div>
            <div style="background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;">
                <p>Hai <b>'.$pemilik['nama'].'</b>,</p>
                <p>Anda telah menerima permintaan pertukaran pakaian melalui sistem <b>ReWear</b>.</p>
                <div style="border:1px solid #ccc;padding:20px;border-radius:8px;margin:20px 0;">
                    <h4 style="margin-top:0;">Maklumat Pertukaran:</h4>
                    <p><b>Pemohon:</b> '.$peminta['nama'].' ('.$peminta['noMatrik'].')</p>
                    <p><b>Tarikh Permintaan:</b> '.date('d-m-Y H:i').'</p>
                    <p><b>Pakaian Anda:</b> '.$item['namaPakaian'].' (Saiz: '.$item['saiz'].')<br><img src="cid:pakaianTarget" width="150"></p>
                    <p><b>Pakaian Ditawarkan:</b> '.$pakaianTukar['namaPakaian'].' (Saiz: '.$pakaianTukar['saiz'].')<br><img src="cid:pakaianTukar" width="150"></p>
                    <p><b>Nota Tambahan:</b> '.(!empty($nota) ? nl2br(htmlspecialchars($nota)) : '<i>Tiada</i>').'</p>
                    <p style="margin-top:25px;">Sila log masuk ke sistem ReWear untuk menyemak dan menguruskan permintaan ini.</p>
                    <div style="margin-top:20px;text-align:center;">
                        <a href="https://amalia.aizathami.website/pemberian_pertukaran.php?tab=permohonan" style="background:#3c7962;color:white;padding:12px 25px;border-radius:50px;text-decoration:none;font-weight:bold;font-size:16px;">Semak Permohonan</a>
                    </div>
                </div>
                <p style="font-size:12px;color:#999;text-align:center;margin-top:30px;">Email ini dihantar secara automatik. Sila jangan balas email ini.</p>
            </div>
        </div>';

      $mail->send();
  } catch (Exception $e) {
      // Email gagal dihantar
  }

  $_SESSION['berjaya_pertukaran'] = true;
  header("Location: borang_pertukaran.php?idPakaian=$idPakaian");
  exit();

}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ReWear: Borang Pertukaran Pakaian</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: linear-gradient(to bottom, #ffffff, #ffffff); }
    .container { max-width: 700px; margin: 50px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.15); }
    .btn-custom { background: #3c7962; color: white; padding: 14px 24px; border-radius: 50px; font-weight: 600; width: 100%; font-size: 16px; transition: 0.3s; }
    .btn-custom:hover { background: #2f5e4c; }
    .form-label { font-weight: 600; color: #3c7962; margin-bottom: 8px; }
    .header-title { font-weight: 700; color: #3c7962; margin-bottom: 30px; text-align: center; }
    .select2-container { width: 100% !important; z-index: 1000 !important; }
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #3c7962; border-radius: 50px; height: 55px; padding: 5px 20px; background-color: #fff; transition: all 0.3s ease;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { 
            line-height: 45px; 
    padding: 0 20px; 
    font-weight: 500; 
    color:#3c7962; 
    display: flex;
    align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 15px !important; right: 20px !important; width: 30px !important; height: 30px !important;
    }
    .select2-search__field { font-family: 'Poppins', sans-serif; font-size: 15px; }
    .select2-dropdown { border-radius: 10px !important; border: 1.5px solid #3c7962 !important; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .select2-results__option { transition: 0.2s; }
    .select2-results__option--highlighted { background-color: #3c7962 !important; color: white !important; }
    .option-item img { border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    @media(max-width: 600px) {
      .container { padding: 25px; margin: 20px; }
      .btn-custom { font-size: 14px; padding: 12px 20px; }
      .header-title { font-size: 1.5rem; }
    }
  </style>
</head>
<body>
<div class="container">
  <a href="javascript:history.back()" class="text-decoration-none mb-3 d-inline-block" style="color: #3c7962; font-weight: 500;"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
  <h2 class="header-title">Borang Pertukaran Pakaian</h2>
  <form action="" method="POST">
    <input type="hidden" name="idPakaianTarget" value="<?= $item['idPakaian'] ?>">
    <label class="form-label">Pakaian Yang Anda Inginkan</label>
    <div class="form-group border rounded p-3 d-flex align-items-center mb-4" style="border: 1.5px solid #3c7962;">
      <img src="<?= $gambarUtama ?>" alt="Gambar Pakaian" class="me-3 rounded" style="width: 120px; height: 120px; object-fit: cover; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
      <div>
        <p class="mb-1 fw-bold">Jenama: <span class="text-muted"> <?= $item['jenama'] ?> </span></p>
        <p class="mb-0 fw-bold">Nama: <span class="text-muted"> <?= $item['namaPakaian'] ?> </span></p>
      </div>
    </div>
    <label class="form-label">Sila Pilih Pakaian Yang Anda Ingin Tukar</label>
    <select class="form-control" id="pakaian_tukar" name="pakaian_tukar" required>
      <option></option>
      <?php foreach ($userItems as $userItem): ?>
        <?php 
          $gambarDropdown = $userItem['gambar'] ? (str_starts_with($userItem['gambar'], 'uploads/') ? $userItem['gambar'] : 'uploads/'.$userItem['gambar']) : 'no_image.jpg';
        ?>
        <option value="<?= $userItem['idPakaian'] ?>" data-img="<?= $gambarDropdown ?>">
          <?= $userItem['namaPakaian'] ?> (<?= $userItem['saiz'] ?>)
        </option>
      <?php endforeach; ?>
    </select>
    <label class="form-label mt-4">Nota Tambahan</label>
    <textarea class="form-control rounded-3" id="nota" name="nota" rows="4" placeholder="Tulis sebarang nota tambahan jika perlu..."></textarea>
    <button type="submit" class="btn-custom mt-5">Hantar Permintaan</button>
  </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('#pakaian_tukar').select2({
      placeholder: "Pakaian Anda",
      allowClear: false,
      dropdownParent: $('.container'),
      templateResult: formatState,
      templateSelection: formatSelection
    });

    function formatState (state) {
      if (!state.id) return state.text;
      var imgSrc = $(state.element).data('img') || 'no_image.jpg';
      var $state = $(
        `<div class="d-flex align-items-center option-item" style="max-width: 100%;">
          <div style="flex-shrink: 0;">
            <img src="${imgSrc}" class="me-3 rounded" style="width:60px;height:60px;object-fit:cover;"/>
          </div>
          <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${state.text}</div>
        </div>`
      );
      return $state;
    }

    function formatSelection (state) {
      if (!state.id) return state.text;
      var imgSrc = $(state.element).data('img') || 'no_image.jpg';
      var $selection = $(
        `<div class="d-flex align-items-center" style="overflow:hidden;">
          <div style="flex-shrink:0;">
            <img src="${imgSrc}" class="me-2 rounded" style="width:40px;height:40px;object-fit:cover;"/>
          </div>
          <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${state.text}</div>
        </div>`
      );
      return $selection;
    }

    $('#pakaian_tukar').on('select2:open', function() {
      setTimeout(function() {
        $('.select2-search__field').attr('placeholder', 'Cari pakaian...');
      }, 100);
    });
  });
</script>
<?php if (isset($_SESSION['berjaya_pertukaran'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berjaya!',
    text: 'Permintaan anda telah berjaya dihantar.',
    confirmButtonColor: '#3c7962',
  }).then(() => {
    window.location.href = 'pemberian_pertukaran.php?tab=permintaan';
  });
</script>
<?php unset($_SESSION['berjaya_pertukaran']); endif; ?>

</body>
</html>