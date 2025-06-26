<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$noMatrik = $_SESSION['no_matrik'];

if (!isset($_GET['id'])) {
    echo "ID tidak sah.";
    exit();
}

$idPertukaran = $_GET['id'];

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("
    SELECT pt.*, 
           t1.namaPakaian AS namaTarget, t1.saiz AS saizTarget, t1.gambar AS gambarTarget, t1.noMatrik AS noMatrikPemilik,
           t2.namaPakaian AS namaTukar, t2.saiz AS saizTukar, t2.jenama, t2.kategori,
           t2.status AS statusPakaian, t2.jenis_pemberian, t2.deskripsi, t2.gambar AS gambarTukar,
           u.nama AS namaPeminta
    FROM tbl_pertukaran pt
    JOIN tbl_pakaian t1 ON pt.idPakaianTarget = t1.idPakaian
    JOIN tbl_pakaian t2 ON pt.idPakaianTukar = t2.idPakaian
    LEFT JOIN tbl_pengguna u ON pt.noMatrikPeminta = u.noMatrik
    WHERE pt.idPertukaran = ?
");

$stmt->execute([$idPertukaran]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Permintaan tidak dijumpai.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Butiran Permintaan Pertukaran</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f9f9f9;
  padding: 0;
  margin: 0;
}

.container-box {
  background: #fff;
  border-radius: 20px;
  padding: 40px;
  max-width: 900px;
  margin: 50px auto;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.img-thumb {
  width: 100%;
  max-width: 220px;
  height: 220px;
  object-fit: contain;
  border-radius: 15px;
  border: 1px solid #ddd;
  background-color: #fafafa;
  padding: 5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-title {
  font-weight: 600;
  font-size: 1.2rem;
  margin-top: 25px;
  color: #3c7962;
}

.status-badge {
  padding: 8px 16px;
  border-radius: 30px;
  font-size: 1rem;
  font-weight: 600;
  text-transform: capitalize;
}

h4 {
  color: #3c7962;
  font-weight: 700; /* tebal betul */
  font-size: 1.8rem; /* besarkan sikit */
  margin-bottom: 35px;
  text-align: center;
  letter-spacing: 0.5px;
}

.btn {
  border-radius: 25px !important;
  padding: 10px 25px;
  font-weight: 600;
  transition: 0.3s;
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


.btn-outline-secondary {
  font-weight: 600;
  border-radius: 25px;
}

hr {
  border-top: 1px solid #ddd;
  margin: 30px 0;
}

.info-label {
  font-weight: 600;
  color: #555;
  margin-bottom: 5px;
}

.info-value {
  margin-bottom: 10px;
  color: #333;
}
  </style>
</head>
<body>
<?php include 'nav_bar_nu.php'; ?>

<div class="container-box">
  <h4 class="mb-4">Butiran Permintaan Pertukaran</h4>
  <div class="row justify-content-center">
    <div class="col-md-5 mb-3">
      <h6 class="section-title">Pakaian Diminta:</h6>
      <div style="position: relative; display: inline-block;">
  <img src="<?= explode(',', $data['gambarTarget'])[0] ?>" class="img-thumb mb-2">
  <?php if ($data['noMatrikPemilik'] == $_SESSION['no_matrik']): ?>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.5); color: white; 
                display: flex; align-items: center; justify-content: center;
                font-weight: 600; border-radius: 15px; font-size: 1.2rem;">
      MILIK ANDA
    </div>
  <?php endif; ?>
</div>

      <p><strong>Nama:</strong> <?= htmlspecialchars($data['namaTarget']) ?></p>
      <p><strong>Saiz:</strong> <?= $data['saizTarget'] ?></p>
    </div>
    <div class="col-md-5 mb-3">
      <h6 class="section-title">Pakaian Ditawarkan:</h6>
      <div style="position: relative; display: inline-block;">
  <img src="<?= explode(',', $data['gambarTukar'])[0] ?>" class="img-thumb mb-2">
  <?php if ($data['noMatrikPeminta'] == $_SESSION['no_matrik']): ?>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.5); color: white; 
                display: flex; align-items: center; justify-content: center;
                font-weight: 600; border-radius: 15px; font-size: 1.2rem;">
      MILIK ANDA
    </div>
  <?php endif; ?>
</div>

      <p><strong>Nama:</strong> <?= $data['namaTukar'] ?></p>
      <p><strong>Saiz:</strong> <?= $data['saizTukar'] ?></p>
      <p><strong>Jenama:</strong> <?= $data['jenama'] ?? '-' ?></p>
      <p><strong>Kategori:</strong> <?= $data['kategori'] ?? '-' ?></p>
      <p><strong>Deskripsi:</strong><br><?= nl2br($data['deskripsi']) ?></p>
    </div>
  </div>

  <hr>

  <p><strong>Pemohon:</strong> <?= htmlspecialchars($data['namaPeminta']) ?> (<?= $data['noMatrikPeminta'] ?>)</p>
  <p><strong>Tarikh Permintaan:</strong> 
    <?= (new DateTime($data['tarikhPermintaan'], new DateTimeZone('UTC')))
          ->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'))
          ->format('d-m-Y H:i') ?>
  </p>
  <p><strong>Status:</strong> 
    <?php
      $badgeClass = "status-badge badge ";
      if ($data['status'] == 'diterima') $badgeClass .= "bg-success";
      elseif ($data['status'] == 'ditolak') $badgeClass .= "bg-danger";
      else $badgeClass .= "bg-warning text-dark";
    ?>
    <span class="<?= $badgeClass ?>"><?= ucfirst($data['status']) ?></span>
  </p>

  <p><strong>Nota Tambahan:</strong><br>
  <?= !empty($data['nota']) ? nl2br(htmlspecialchars($data['nota'])) : '<i>Tiada nota tambahan</i>'; ?>
  </p>

  <?php if ($data['status'] == 'diterima' && $data['noMatrikPemilik'] != $noMatrik): ?>
  <a href="paparan_mesej.php?chat_with=<?= $data['noMatrikPemilik'] ?>&idPakaian=<?= $data['idPakaianTarget'] ?>" class="btn-custom">
    💬 Mesej Pemilik
  </a>
  <?php elseif ($data['status'] == 'diterima' && $data['noMatrikPemilik'] == $noMatrik): ?>
    <a href="paparan_mesej.php?chat_with=<?= $data['noMatrikPeminta'] ?>&idPakaian=<?= $data['idPakaianTarget'] ?>" class="btn-custom">
      💬 Mesej Pemohon
    </a>
  <?php endif; ?>

  <?php if ($data['noMatrikPemilik'] == $noMatrik && $data['status'] == 'menunggu'): ?>
    <form action="kemaskini_pertukaran.php" method="POST" class="d-inline">
      <input type="hidden" name="idPertukaran" value="<?= $data['idPertukaran'] ?>">
      <button type="submit" name="status" value="diterima" class="btn btn-success btn-sm">✔ Terima</button>
    </form>
    <form action="kemaskini_pertukaran.php" method="POST" class="d-inline ms-2">
      <input type="hidden" name="idPertukaran" value="<?= $data['idPertukaran'] ?>">
      <button type="submit" name="status" value="ditolak" class="btn btn-danger btn-sm">✖ Tolak</button>
    </form>
  <?php endif; ?>

  <br><br>
  <a href="javascript:history.back()" class="btn btn-outline-secondary mt-3">← Kembali</a>
</div>

</body>
</html>
