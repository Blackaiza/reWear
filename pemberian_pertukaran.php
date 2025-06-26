<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$noMatrik = $_SESSION['no_matrik'];

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("
    SELECT pt.*, 
           t1.namaPakaian AS namaTarget, t1.saiz AS saizTarget, t1.gambar AS gambarTarget,
           t2.namaPakaian AS namaTukar, t2.saiz AS saizTukar
    FROM tbl_pertukaran pt
    JOIN tbl_pakaian t1 ON pt.idPakaianTarget = t1.idPakaian
    JOIN tbl_pakaian t2 ON pt.idPakaianTukar = t2.idPakaian
    WHERE pt.noMatrikPeminta = ?
    ORDER BY pt.tarikhPermintaan DESC
");
$stmt->execute([$noMatrik]);
$permintaanSaya = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtStatus = $conn->prepare("
    SELECT pt.*, 
           t1.namaPakaian AS namaTarget, t1.saiz AS saizTarget, t1.gambar AS gambarTarget,
           t2.namaPakaian AS namaTukar, t2.saiz AS saizTukar,
           u.nama AS namaPeminta
    FROM tbl_pertukaran pt
    JOIN tbl_pakaian t1 ON pt.idPakaianTarget = t1.idPakaian
    JOIN tbl_pakaian t2 ON pt.idPakaianTukar = t2.idPakaian
    LEFT JOIN tbl_pengguna u ON pt.noMatrikPeminta = u.noMatrik
    WHERE t1.noMatrik = ?
    ORDER BY pt.tarikhPermintaan DESC
");
$stmtStatus->execute([$noMatrik]);
$statusSaya = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);

$pendingCount = 0;
foreach ($statusSaya as $row) {
    if (strtolower($row['status']) === 'menunggu') {
        $pendingCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <title>Pertukaran</title>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f5f5f5; }
    .container-card { background-color: #fff; border-radius: 15px; padding: 40px; max-width: 1200px; margin: 50px auto; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    h3.title { text-align: center; font-weight: 700; color: #3c7962; margin-bottom: 30px; font-size: 32px; }
    .nav-tabs .nav-link.active { background-color: #3c7962 !important; color: white !important; border-radius: 50px; font-weight: 500; }
    .nav-tabs .nav-link { border: none; color: #3c7962; font-weight: 500; }
    .badge { font-size: 13px; padding: 8px 16px; border-radius: 50px; font-weight: 500; }
    .badge-warning { background-color: #ffe39c !important; color: #856404 !important; }
    .badge-success { background-color: #7dd87d !important; color: #fff !important; }
    .badge-danger { background-color: #e06666 !important; color: #fff !important; }
    .table { box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .table th, .table td { vertical-align: middle !important; }
    .btn { border-radius: 30px; }
    .btn:hover { opacity: 0.85; }
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
<h3 class="title">PERTUKARAN PAKAIAN</h3>
<ul class="nav nav-tabs justify-content-center mb-4" id="pertukaranTabs">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#permintaan">Permintaan Saya</button>
  </li>
<li class="nav-item">
  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#permohonan">
    Permintaan Masuk
    <?php if ($pendingCount > 0): ?>
      <span id="pending-badge" class="badge bg-danger ms-1"><?= $pendingCount ?></span>
    <?php endif; ?>
  </button>
</li>

</ul>
<div class="tab-content">
  <div class="tab-pane fade show active" id="permintaan">
    <div id="permintaan-container">
    <?php if (empty($permintaanSaya)): ?>
      <div class="alert alert-secondary text-center">Tiada Permintaan Dibuat</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>Pakaian Diminta</th><th>Pakaian Ditawar</th><th>Status</th><th>Tarikh</th><th>Butiran</th><th>Tindakan</th></tr>
          </thead>
          <tbody>
            <?php foreach ($permintaanSaya as $row): 
              $status = strtolower($row['status']);
              $class = ($status=='menunggu')?"badge-warning":(($status=='diterima')?"badge-success":"badge-danger");
            ?>
            <tr>
              <td><?= $row['namaTarget'] ?></td>
              <td><?= $row['namaTukar'] ?></td>
              <td><span class="badge <?= $class ?>"><?= ucfirst($row['status']) ?></span></td>
              <td><?= date('d-m-Y H:i', strtotime($row['tarikhPermintaan'])) ?></td>
              <td><a href="detail_pertukaran.php?id=<?= $row['idPertukaran'] ?>" class="btn btn-outline-secondary btn-sm">Lihat</a></td>
              <td>
                <?php if ($row['status'] === 'Menunggu'): ?>
                  <button class="btn btn-danger btn-sm batal-btn" data-id="<?= $row['idPertukaran'] ?>">Batalkan</button>
                <?php elseif (strtolower($row['status']) === 'diterima'): ?>
                  <?php if ($noMatrik === $row['noMatrikPemilik']): ?>
                    <a href="paparan_mesej.php?chat_with=<?= $row['noMatrikPeminta'] ?>&idPakaian=<?= $row['idPakaianTarget'] ?>" class="btn-mesej">
                      <i class="fas fa-envelope"></i> Mesej Pemohon
                    </a>
                  <?php elseif ($noMatrik === $row['noMatrikPeminta']): ?>
                    <a href="paparan_mesej.php?chat_with=<?= $row['noMatrikPemilik'] ?>&idPakaian=<?= $row['idPakaianTarget'] ?>" class="btn-mesej">
                      <i class="fas fa-envelope"></i> Mesej Pemilik
                    </a>
                  <?php endif; ?>
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
    </div>
  </div>

  <div class="tab-pane fade" id="permohonan">
    <div id="permohonan-container">
    <?php if (empty($statusSaya)): ?>
      <div class="alert alert-secondary text-center">Tiada permintaan terhadap pakaian anda.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>Pemohon</th><th>Pakaian Ditawarkan</th><th>Tarikh</th><th>Butiran</th><th>Status</th><th>Tindakan</th></tr>
          </thead>
          <tbody>
            <?php foreach ($statusSaya as $row): 
              $status = strtolower($row['status']);
              $class = ($status=='menunggu')?"badge-warning":(($status=='diterima')?"badge-success":"badge-danger");
            ?>
            <tr id="row-<?= $row['idPertukaran'] ?>">
              <td><?= htmlspecialchars($row['namaPeminta']) ?> (<?= $row['noMatrikPeminta'] ?>)</td>
              <td><?= $row['namaTukar'] ?></td>
              <td><?= date('d-m-Y H:i', strtotime($row['tarikhPermintaan'])) ?></td>
              <td><a href="detail_pertukaran.php?id=<?= $row['idPertukaran'] ?>" class="btn btn-outline-secondary btn-sm">Lihat</a></td>
              <td><span id="badge-<?= $row['idPertukaran'] ?>" class="badge <?= $class ?>"><?= ucfirst($row['status']) ?></span></td>
              <td>
                <?php if ($row['status'] === 'menunggu'): ?>
                <button class="btn btn-success btn-sm action-btn" data-id="<?= $row['idPertukaran'] ?>" data-status="diterima">✔ Terima</button>
                <button class="btn btn-danger btn-sm action-btn ms-2" data-id="<?= $row['idPertukaran'] ?>" data-status="ditolak">✖ Tolak</button>
                <?php else: ?>
                <span class="text-muted">Telah <?= $row['status'] ?></span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
    </div>
  </div>
</div>
</div>

<script>
$(document).ready(function() {
  $('.action-btn').click(function() {
    var id = $(this).data('id');
    var status = $(this).data('status');
    Swal.fire({
      title: 'Anda pasti?',
      text: 'Anda akan '+status.toUpperCase()+' permintaan ini.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3c7962',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, teruskan!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('kemaskini_pertukaran.php', {idPertukaran: id, status: status}, function(res) {
          $('#badge-'+id).removeClass('badge-warning badge-success badge-danger').addClass(status === 'diterima' ? 'badge-success' : 'badge-danger').text(status.charAt(0).toUpperCase() + status.slice(1));
          $('#row-'+id+' .action-btn').remove();
          $('#row-'+id+' td:last').append('<span class="text-muted">Telah '+status+'</span>');
          var current = parseInt($('#pending-badge').text());
          if (current > 0) {
            $('#pending-badge').text(current - 1);
            if(current - 1 === 0) { $('#pending-badge').hide(); }
          }
        }).fail(function() {
          Swal.fire('Ralat!','Sistem gagal mengemaskini.','error');
        });
      }
    });
  });

  $('.cancel-btn').click(function() {
    var id = $(this).data('id');
    Swal.fire({
      title: 'Batalkan permintaan?',
      text: 'Anda pasti ingin membatalkan permintaan ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, batalkan'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('batal_pertukaran.php', {idPertukaran: id}, function(res) {
          Swal.fire('Berjaya!','Permintaan telah dibatalkan.','success').then(() => location.reload());
        }).fail(function() {
          Swal.fire('Ralat','Sistem gagal membatalkan.','error');
        });
      }
    });
  });

  setInterval(function() {
    $.get('kemaskini_pertukaran_data.php?permintaan=1', function(res) {
      $('#permintaan-container').html(res);
    });
  }, 5000);
});
</script>
</body>
</html>
