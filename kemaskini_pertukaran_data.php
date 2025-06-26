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

$stmt = $conn->prepare("SELECT pt.*, 
           t1.namaPakaian AS namaTarget, t1.saiz AS saizTarget, 
           t2.namaPakaian AS namaTukar, t2.saiz AS saizTukar
    FROM tbl_pertukaran pt
    JOIN tbl_pakaian t1 ON pt.idPakaianTarget = t1.idPakaian
    JOIN tbl_pakaian t2 ON pt.idPakaianTukar = t2.idPakaian
    WHERE pt.noMatrikPeminta = ?
    ORDER BY pt.tarikhPermintaan DESC");
$stmt->execute([$noMatrik]);
$permintaanSaya = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="table table-borderless">
  <thead class="table-light">
    <tr><th>Pakaian Diminta</th><th>Pakaian Ditawar</th><th>Status</th><th>Tarikh</th><th>Butiran</th><th>Tindakan</th></tr>
  </thead>
  <tbody>
    <?php foreach ($permintaanSaya as $row): 
      $status = strtolower($row['status']);
      $class = ($status=='menunggu')?"bg-warning text-dark":(($status=='diterima')?"bg-success":"bg-danger");
    ?>
    <tr>
      <td><?= $row['namaTarget'] ?></td>
      <td><?= $row['namaTukar'] ?></td>
      <td><span class="badge <?= $class ?>"><?= ucfirst($row['status']) ?></span></td>
      <td><?= date('d-m-Y H:i', strtotime($row['tarikhPermintaan'])) ?></td>
      <td><a href="detail_pertukaran.php?id=<?= $row['idPertukaran'] ?>" class="btn btn-outline-secondary btn-sm">Lihat</a></td>
      <td>
        <?php if ($status == 'menunggu'): ?>
          <button class="btn btn-danger btn-sm cancel-btn" data-id="<?= $row['idPertukaran'] ?>">Batalkan</button>
        <?php elseif ($status == 'diterima'): ?>
          <?php if ($noMatrik === $row['noMatrikPemilik']): ?>
            <a href="paparan_mesej.php?chat_with=<?= $row['noMatrikPeminta'] ?>&idPakaian=<?= $row['idPakaianTarget'] ?>" class="btn-mesej">
              <i class="fas fa-envelope"></i> Mesej Pemohon
            </a>
          <?php elseif ($noMatrik === $row['noMatrikPeminta']): ?>
            <a href="paparan_mesej.php?chat_with=<?= $row['noMatrikPemilik'] ?>&idPakaian=<?= $row['idPakaianTarget'] ?>" class="btn-mesej">
              <i class="fas fa-envelope"></i> Mesej Pemilik
            </a>
          <?php else: ?>
            -
          <?php endif; ?>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>

    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<script>
$(document).ready(function() {
  $('.cancel-btn').click(function() {
    var id = $(this).data('id');
    Swal.fire({
      title: 'Batalkan Permintaan?',
      text: 'Permintaan ini akan dipadam.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, batalkan'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('batal_pertukaran.php', {idPertukaran: id}, function(res) {
          if (res.success) {
            Swal.fire('Berjaya', 'Permintaan berjaya dibatalkan.', 'success').then(() => location.reload());
          } else {
            Swal.fire('Ralat', 'Gagal membatalkan permintaan.', 'error');
          }
        }, 'json').fail(function() {
          Swal.fire('Ralat', 'Server error.', 'error');
        });
      }
    });
  });
});
</script>
