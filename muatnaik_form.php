<?php
session_start();
include 'database.php';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$editrow = null;
if (isset($_GET['edit'])) {
    $product_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM tbl_pakaian WHERE idPakaian = ?");
    $stmt->execute([$product_id]);
    $editrow = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8" />
<title>ReWear: Muat Naik Pakaian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: 'Poppins', sans-serif; background: url('bg.jpg') no-repeat center center fixed; color: #333; min-height: 100vh;}
.container { max-width: 1000px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); margin-top: 30px; }
.form-control, .form-select { border-radius: 5px; border: 1px solid #3c7962; }
.title-bold { font-weight: 700; text-align: center; color: #3c7962; margin-bottom: 20px; }
.preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
.preview-box { position: relative; width: 120px; height: 120px; border: 1px solid #ccc; border-radius: 10px; overflow: hidden; background: rgba(204, 236, 229, 0.42); display: flex; align-items: center; justify-content: center; cursor: pointer; }
.preview-box img { width: 100%; height: 100%; object-fit: cover; }
.preview-box span { color: #3c7962; font-weight: bold; text-align: center; }
.btn-remove { position: absolute; top: 4px; right: 4px; background: rgb(104, 100, 100); border: none; color: white; padding: 2px 6px; border-radius: 50%; font-weight: bold; cursor: pointer; }
</style>
</head>
<body>
<?php include 'nav_bar_nu.php'; ?>
<div class="container">
<h2 class="title-bold"><?php echo isset($_GET['edit']) ? 'KEMASKINI PAKAIAN' : 'MUAT NAIK PAKAIAN'; ?></h2>
<form id="uploadForm" method="POST" enctype="multipart/form-data" action="muatnaik_process.php<?php echo isset($_GET['edit']) ? '?edit=' . $_GET['edit'] : ''; ?>">
<input type="hidden" name="noMatrik" value="<?php echo $_SESSION['no_matrik']; ?>">
<div class="mb-3">
<label>Nama Pakaian</label>
<input type="text" class="form-control" name="namaPakaian" placeholder="Contoh: Baju Kurung Moden" value="<?php echo $editrow['namaPakaian'] ?? ''; ?>" required>
</div>
<div class="mb-3">
<label>Jenama</label>
<input type="text" class="form-control" name="jenama" placeholder="Contoh: Padini, Uniqlo" value="<?php echo $editrow['jenama'] ?? ''; ?>" required>
</div>
<div class="mb-3">
<label>Saiz</label>
<select class="form-select" name="saiz" required>
<option value="" disabled hidden <?= empty($editrow['saiz']) ? 'selected' : '' ?>>Sila pilih saiz</option>
<?php foreach (['S', 'M', 'L', 'XL','2XL', '3XL'] as $size): ?>
<option value="<?= $size ?>" <?= ($editrow['saiz'] ?? '') == $size ? 'selected' : '' ?>><?= $size ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="mb-3">
<label>Kategori</label>
<select class="form-select" name="kategori" required>
<option value="" disabled hidden <?= empty($editrow['kategori']) ? 'selected' : '' ?>>Sila pilih kategori</option>
<?php foreach (['Lelaki', 'Wanita'] as $cat): ?>
<option value="<?= $cat ?>" <?= ($editrow['kategori'] ?? '') == $cat ? 'selected' : '' ?>><?= $cat ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="mb-3">
<label>Status</label>
<select class="form-select" name="status" required>
<option value="" disabled hidden <?= empty($editrow['status']) ? 'selected' : '' ?>>Sila pilih status</option>
<option value="Tersedia" <?= ($editrow['status'] ?? '') == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
<option value="Tidak Tersedia" <?= ($editrow['status'] ?? '') == 'Tidak Tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
</select>
</div>
<div class="mb-3">
<label>Jenis Pemberian</label>
<select class="form-select" name="jenis_pemberian" required>
<option value="" disabled hidden <?= empty($editrow['jenis_pemberian']) ? 'selected' : '' ?>>Jenis Pemberian</option>
<option value="Percuma" <?= ($editrow['jenis_pemberian'] ?? '') == 'Percuma' ? 'selected' : '' ?>>Percuma</option>
<option value="Pertukaran" <?= ($editrow['jenis_pemberian'] ?? '') == 'Pertukaran' ? 'selected' : '' ?>>Pertukaran</option>
</select>
</div>
<div class="mb-3">
<label>Deskripsi</label>
<textarea class="form-control" name="deskripsi" rows="3" placeholder="Contoh: labuh 30 cm&#10;lebar 73 cm" required><?php echo $editrow['deskripsi'] ?? ''; ?></textarea>
</div>
<div class="mb-3">
<label>Muat Naik Gambar</label>
<input type="file" id="gambarInput" name="gambar[]" accept=".jpg,.jpeg,.png" multiple style="display: none;">
<div id="previewArea" class="preview-container">
<?php if(isset($editrow['gambar']) && !empty($editrow['gambar'])): foreach(explode(',', $editrow['gambar']) as $img): ?>
<div class="preview-box old-image" data-img="<?php echo $img; ?>">
<img src="<?php echo $img; ?>" class="img-old">
<button type="button" class="btn-remove" onclick="removeOldImage(this)">×</button>
<input type="hidden" name="gambar_lama[]" value="<?php echo $img; ?>">
</div>
<?php endforeach; endif; ?>
<div class="preview-box upload-box" onclick="triggerFileInput()">
<span>+<br>Tambah Gambar</span>
</div>
</div>
</div>
<div class="text-center">
<button type="submit" class="btn btn-success"><?php echo isset($_GET['edit']) ? 'Kemaskini' : 'Muat Naik'; ?></button>
</div>
</form>
</div>

<script>
const validTypes = ['image/jpeg', 'image/png'];
const maxSize = 2 * 1024 * 1024;
let selectedFiles = [];
let selectedHashes = [];
let gambarLamaHash = [];

// Hash gambar lama
window.addEventListener("DOMContentLoaded", async () => {
    const imgEls = document.querySelectorAll('.img-old');
    for (const img of imgEls) {
        const hash = await getImageHash(img.src);
        if (hash) gambarLamaHash.push(hash);
    }
});

function triggerFileInput() {
    document.getElementById('gambarInput').click();
}

document.getElementById('gambarInput').addEventListener('change', function(e) {
    const previewArea = document.getElementById('previewArea');
    Array.from(e.target.files).forEach(async (file) => {
        if (!validTypes.includes(file.type)) {
            alert('Jenis fail tidak dibenarkan. Hanya JPG dan PNG.');
            return;
        }
        if (file.size > maxSize) {
            alert('Fail terlalu besar. Maksimum saiz 2MB.');
            return;
        }
        const hash = await getFileHash(file);
        if (gambarLamaHash.includes(hash)) {
            alert('Gambar ini sudah ada dalam senarai lama.');
            return;
        }
        if (selectedHashes.includes(hash)) {
            alert('Fail sama telah dimasukkan dalam sesi ini.');
            return;
        }
        selectedFiles.push(file);
        selectedHashes.push(hash);
        const reader = new FileReader();
        reader.onload = function(event) {
            const div = document.createElement('div');
            div.classList.add('preview-box', 'new-image');
            div.innerHTML = `<img src="${event.target.result}"><button type="button" class="btn-remove" onclick="removeNewImage(this, ${selectedFiles.length - 1})">×</button>`;
            previewArea.insertBefore(div, previewArea.lastElementChild);
        };
        reader.readAsDataURL(file);
    });
    e.target.value = '';
});

function removeNewImage(button, index) {
    button.closest('.new-image').remove();
    selectedFiles[index] = null;
    selectedHashes[index] = null;
}

function removeOldImage(button) {
    button.closest('.old-image').remove();
}

document.getElementById('uploadForm').addEventListener('submit', function (e) {
    const input = document.getElementById('gambarInput');
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => { if (file) dataTransfer.items.add(file); });
    input.files = dataTransfer.files;
    const jumlahBaru = selectedFiles.filter(f => f).length;
    const jumlahLama = document.querySelectorAll('.old-image').length;
    if (jumlahBaru + jumlahLama === 0) {
        e.preventDefault();
        alert("Sila muat naik sekurang-kurangnya satu gambar.");
    }
});

async function getFileHash(file) {
    const buffer = await file.arrayBuffer();
    const hashBuffer = await crypto.subtle.digest('SHA-1', buffer);
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

async function getImageHash(url) {
    try {
        const res = await fetch(url);
        const blob = await res.blob();
        const buffer = await blob.arrayBuffer();
        const hashBuffer = await crypto.subtle.digest('SHA-1', buffer);
        return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
    } catch { return null; }
}
</script>
</body>
</html>
