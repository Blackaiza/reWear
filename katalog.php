<?php
session_start();
include 'database.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$kategoriFilter = isset($_GET['kategori']) ? explode(',', $_GET['kategori']) : [];
$saizFilter = isset($_GET['saiz']) ? explode(',', $_GET['saiz']) : [];
$jenisFilter = isset($_GET['jenis']) ? explode(',', $_GET['jenis']) : [];

$query = "
    SELECT * FROM tbl_pakaian p
    WHERE p.status = 'Tersedia'
    AND NOT EXISTS (
        SELECT 1 FROM tbl_percuma perc WHERE perc.idPakaian = p.idPakaian AND perc.status = 'Diterima'
    )
    AND NOT EXISTS (
        SELECT 1 FROM tbl_pertukaran pt WHERE pt.idPakaianTarget = p.idPakaian AND pt.status IN ('menunggu', 'diterima')
    )
";

$params = [];

if (!empty($_GET['search'])) {
    $query .= " AND (namaPakaian LIKE ? OR jenama LIKE ?)";
    $keyword = '%' . $_GET['search'] . '%';
    $params[] = $keyword; $params[] = $keyword;
}

if (!empty($kategoriFilter)) {
    $placeholders = implode(',', array_fill(0, count($kategoriFilter), '?'));
    $query .= " AND kategori IN ($placeholders)";
    $params = array_merge($params, $kategoriFilter);
}

if (!empty($saizFilter)) {
    $placeholders = implode(',', array_fill(0, count($saizFilter), '?'));
    $query .= " AND saiz IN ($placeholders)";
    $params = array_merge($params, $saizFilter);
}

if (!empty($jenisFilter)) {
    $placeholders = implode(',', array_fill(0, count($jenisFilter), '?'));
    $query .= " AND jenis_pemberian IN ($placeholders)";
    $params = array_merge($params, $jenisFilter);
}

$query .= " ORDER BY tarikh_muatnaik DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();
$tiadaItem = empty($items);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ReWear: Katalog</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: 'Poppins', sans-serif; background: #fff; color: #333; margin: 0; padding: 0; }
.container { max-width: 1600px; margin: auto; padding: 20px; }
.catalog-items { 
  display: grid; 
  grid-template-columns: repeat(4, 250px); 
  justify-content: center; 
  gap: 30px; 
  max-width: 1200px; 
  margin: 20px auto 80px; 
  padding: 0 20px;
}

.catalog-item { background: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.3s; text-align: center; padding: 15px; }
.catalog-item:hover { transform: scale(1.03); box-shadow: 0 10px 15px rgba(0,0,0,0.15); }
.catalog-item img { width: 100%; height: 250px; object-fit: cover; border-radius: 15px; }
.catalog-item h5 { margin-top: 10px; font-size: 1.2em; color: #3c7962; font-weight: 600; }
.catalog-item p { margin-top: 5px; color: #666; }
.filter-wrapper-container { max-width: 1200px; margin: 0 auto 10px; padding: 0 10px; }
.filter-wrapper { background: #f9f9f9; border: 1px solid #ddd; border-radius: 20px; box-shadow: 0 8px 15px rgba(0,0,0,0.1); padding: 10px 20px; }
.filter-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; }
.filter-bar .d-flex { gap: 10px; flex-wrap: wrap; }

.filter-pill { background: #fff; border: 1px solid #ccc; border-radius: 30px; padding: 8px 20px; font-weight: 500; transition: 0.3s; cursor: pointer; display: flex; align-items: center; gap: 8px; }
.filter-pill:hover { border-color:rgb(41, 93, 86); color: #3c7962; }
.filter-dropdown { position: relative; display: inline-block; }
.filter-dropdown-menu { display: none; position: absolute; z-index: 10; background: #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.15); border-radius: 15px; padding: 15px; min-width: 200px; border: none; }
.filter-dropdown-menu.show { display: block; }
.clear-all-btn { color:rgb(40, 127, 116); font-weight: 600; cursor: pointer; font-size: 1rem; background: none; border: none; padding: 0; white-space: nowrap; }
.clear-all-btn:hover { text-decoration: underline; color:#00bfa6; }
.upload-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #3c7962;
  color: #fff;
  font-size: 2rem;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
  transition: background-color 0.3s;
}

.upload-button:hover {
  background-color: #2c6c5f;
}

.upload-button i {
  font-size: 1.5rem;
}

@media (max-width: 768px) {
  .filter-bar { flex-direction: column; align-items: stretch; }
  .clear-all-btn { text-align: center; margin-top: 10px; }
  .catalog-items { grid-template-columns: repeat(2, 1fr); gap: 15px; }
  .filter-bar {
    flex-wrap: nowrap;
    justify-content: flex-start;
  }

  .clear-all-btn {
    margin-left: 20px;
    margin-top: 0;  /* Pastikan tidak ada margin atas */
  }

  .filter-bar .d-flex {
    flex-wrap: nowrap;
    gap: 15px; /* Menjaga jarak yang kemas antara elemen */

}
}
.title-bold {
            font-weight: 700;
            text-align: center;
            color: #3c7962;
            margin-bottom: 20px;
        }
</style>
</head>
<body>
<?php include 'nav_bar_nu.php'; ?>
<div class="container">
<h2 class="title-bold">KATALOG</h2>
<?php if (!empty($_GET['search'])): ?>
<p class="text-center text-muted">Carian untuk: <strong><?= htmlspecialchars($_GET['search']) ?></strong></p>
<?php endif; ?>
<div class="filter-wrapper-container">
  <div class="filter-wrapper my-2">
    <div class="filter-bar">
      <div class="d-flex">
        <div class="filter-dropdown">
          <div class="btn filter-pill" data-target="kategoriMenu">Kategori <i class="fas fa-angle-down"></i></div>
          <div id="kategoriMenu" class="filter-dropdown-menu">
            <div class="form-check">
              <input type="checkbox" class="form-check-input kategori-filter" value="Lelaki" <?= in_array('Lelaki', $kategoriFilter) ? 'checked' : '' ?>>
              <label class="form-check-label">Lelaki</label>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input kategori-filter" value="Wanita" <?= in_array('Wanita', $kategoriFilter) ? 'checked' : '' ?>>
              <label class="form-check-label">Wanita</label>
            </div>
          </div>
        </div>
        <div class="filter-dropdown">
          <div class="btn filter-pill" data-target="saizMenu">Saiz <i class="fas fa-angle-down"></i></div>
          <div id="saizMenu" class="filter-dropdown-menu">
            <?php foreach (["S","M","L","XL","2XL","3XL"] as $saiz): ?>
            <div class="form-check">
              <input type="checkbox" class="form-check-input saiz-filter" value="<?= $saiz ?>" <?= in_array($saiz, $saizFilter) ? 'checked' : '' ?>>
              <label class="form-check-label"><?= $saiz ?></label>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="filter-dropdown">
          <div class="btn filter-pill" data-target="jenisMenu">Jenis Pemberian <i class="fas fa-angle-down"></i></div>
          <div id="jenisMenu" class="filter-dropdown-menu">
            <div class="form-check">
              <input type="checkbox" class="form-check-input jenis-filter" value="percuma" <?= in_array('percuma', $jenisFilter) ? 'checked' : '' ?>>
              <label class="form-check-label">Percuma</label>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input jenis-filter" value="pertukaran" <?= in_array('pertukaran', $jenisFilter) ? 'checked' : '' ?>>
              <label class="form-check-label">Pertukaran</label>
            </div>
          </div>
        </div>
      </div>
      <div><span id="resetBtn" class="clear-all-btn">Set Semula</span></div>
    </div>
  </div>
</div>
<div class="catalog-items">
<?php if ($tiadaItem): ?>
  <div class="text-center" style="padding:100px 0;">
    <i class="fas fa-search-minus" style="font-size:3em; color:#aaa;"></i>
    <p style="color:#888;">Tiada item dijumpai.</p>
  </div>
<?php else: ?>
<?php 
$milikAnda = array_filter($items, fn($item) => $item['noMatrik'] == $_SESSION['no_matrik']);
$bukanMilikAnda = array_filter($items, fn($item) => $item['noMatrik'] != $_SESSION['no_matrik']);
?>
<?php foreach ($bukanMilikAnda as $item): ?>
  <a href="detail.php?id=<?= $item['idPakaian'] ?>" style="text-decoration: none; color: inherit;">
    <div class="catalog-item">
      <?php $gambar = explode(',', $item['gambar'])[0]; ?>
      <div style="position:relative;">
        <img src="<?= $gambar ?>" alt="Image">
        <div style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,0.7);color:#fff;padding:5px 10px;border-radius:5px;font-size:0.8em;">
          <?= strtoupper($item['jenis_pemberian']) ?>
        </div>
      </div>
      <h5><?= strtoupper($item['jenama']) ?></h5>
      <p><?= ucwords(strtolower($item['namaPakaian'])) ?></p>
    </div>
  </a>
<?php endforeach; ?>
<?php foreach ($milikAnda as $item): ?>
  <a href="detail.php?id=<?= $item['idPakaian'] ?>" style="text-decoration: none; color: inherit;">
    <div class="catalog-item">
      <?php $gambar = explode(',', $item['gambar'])[0]; ?>
      <div style="position:relative;">
        <img src="<?= $gambar ?>" alt="Image">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;border-radius:10px;">
          <span style="color:#fff;font-weight:600;font-size:1em;">MILIK ANDA</span>
        </div>
      </div>
      <h5><?= strtoupper($item['jenama']) ?></h5>
      <p><?= ucwords(strtolower($item['namaPakaian'])) ?></p>
    </div>
  </a>
<?php endforeach; ?>
<?php endif; ?>
</div>

<!-- Butang Muat Naik Pakaian -->
<a href="muatnaik_form.php" class="upload-button">
  <i class="fas fa-plus"></i>
</a>

<script>
document.querySelectorAll('.filter-pill').forEach(button => {
  button.addEventListener('click', function() {
    const targetId = this.getAttribute('data-target');
    document.querySelectorAll('.filter-dropdown-menu').forEach(menu => {
      if (menu.id === targetId) {
        menu.classList.toggle('show');
      } else {
        menu.classList.remove('show');
      }
    });
  });
});

window.addEventListener('click', function(event) {
  if (!event.target.closest('.filter-dropdown') && !event.target.closest('.filter-dropdown-menu')) {
    document.querySelectorAll('.filter-dropdown-menu').forEach(menu => {
      menu.classList.remove('show');
    });
  }
});

const kategoriCheckboxes = document.querySelectorAll('.kategori-filter');
const saizCheckboxes = document.querySelectorAll('.saiz-filter');
const jenisCheckboxes = document.querySelectorAll('.jenis-filter');

function filterCatalog() {
  setTimeout(function() {
    const kategori = Array.from(kategoriCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    const saiz = Array.from(saizCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    const jenis = Array.from(jenisCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    const query = new URLSearchParams();
    if (kategori.length) query.append('kategori', kategori.join(','));
    if (saiz.length) query.append('saiz', saiz.join(','));
    if (jenis.length) query.append('jenis', jenis.join(','));
    window.location.search = query.toString();
  }, 200);
}

document.getElementById('resetBtn').addEventListener('click', () => window.location.href = window.location.pathname);
kategoriCheckboxes.forEach(cb => cb.addEventListener('change', filterCatalog));
saizCheckboxes.forEach(cb => cb.addEventListener('change', filterCatalog));
jenisCheckboxes.forEach(cb => cb.addEventListener('change', filterCatalog));

// Fungsi untuk menangani carian
document.querySelector('.search-button').addEventListener('click', function() {
  const query = document.querySelector('.search-input').value;
  // Lakukan tindakan untuk mengendalikan carian (contohnya, menghantar ke server atau menapis hasil)
  console.log(query);
});


</script>

</body>
</html>
