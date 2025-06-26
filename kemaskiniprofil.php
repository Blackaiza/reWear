<?php
session_start();
include 'database.php';

// Pastikan user login
if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$no_matrik = $_SESSION['no_matrik'];

// WAJIB: Ambil maklumat pengguna sebelum navbar dan HTML
$stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE noMatrik = ?");
$stmt->execute([$no_matrik]);
$pengguna = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Fungsi padam gambar
    if (isset($_POST['padam_gambar'])) {
        if (!empty($pengguna['gambar_profil']) && file_exists($pengguna['gambar_profil']) && $pengguna['gambar_profil'] !== 'profile-user.png') {
            unlink($pengguna['gambar_profil']);
        }

        $stmt = $conn->prepare("UPDATE tbl_pengguna SET gambar_profil = NULL WHERE noMatrik = ?");
        $stmt->execute([$no_matrik]);

        echo "<script>alert('Gambar profil berjaya dipadam.'); window.location.href='kemaskiniprofil.php';</script>";
        exit();
    }

    // Kemas kini profil
    $no_telefon = $_POST['no_telefon'];
    $gambar_profil = $pengguna['gambar_profil'];

    // Handle upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $gambar_dir = 'uploads/profil/';
        if (!is_dir($gambar_dir)) {
            mkdir($gambar_dir, 0777, true);
        }

        if (!empty($gambar_profil) && file_exists($gambar_profil) && $gambar_profil !== 'profile-user.png') {
            unlink($gambar_profil);
        }

        $gambar_name = time() . '_' . basename($_FILES['gambar']['name']);
        $gambar_path = $gambar_dir . $gambar_name;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $gambar_path)) {
            $gambar_profil = $gambar_path;
        } else {
            echo "<script>alert('Gagal muat naik gambar.');</script>";
        }
    }

    // Kemaskini DB
    $stmt = $conn->prepare("UPDATE tbl_pengguna SET no_telefon = ?, gambar_profil = ? WHERE noMatrik = ?");
    $stmt->execute([$no_telefon, $gambar_profil, $no_matrik]);
    echo "<script>alert('Profil berjaya dikemaskini!'); window.location.href='kemaskiniprofil.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kemaskini Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('bg.jpg') no-repeat center center fixed;
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }

        .btn-custom {
            background: #3c7962;
            color: #fff;
            font-weight: 600;
            border-radius: 25px;
            padding: 10px 30px;
        }

        .btn-custom:hover {
            background: #2C4230;
        }

        .profile-section {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: center;
        }

        .profile-picture {
            flex: 1;
            text-align: center;
        }

        .profile-picture img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            
        }

        .profile-info {
            flex: 2;
        }

        .title-section {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 30px;
        }

        .title-section h2 {
            font-weight: bold;
            color: #3c7962;
            margin-left: 20px;
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
    .btn-custom2 {
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

    .btn-custom2:hover {
      background-color: #2C4230;
      color: #fff;
    }
    </style>
</head>
<body>

<?php include 'nav_bar_nu.php'; ?>

<div class="container">
    <div class="title-section">
        <h2>KEMASKINI PROFIL</h2>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="profile-section">
<div class="profile-picture">
    <?php
        $gambar_url = (!empty($pengguna['gambar_profil']) && file_exists(__DIR__ . '/' . $pengguna['gambar_profil']))
            ? $pengguna['gambar_profil']
            : 'profile-user.png';
    ?>
    <img id="preview-img" src="<?= $gambar_url ?>" alt="Profile Picture" class="mb-3">

    <input type="file" name="gambar" accept="image/*" class="form-control mb-3" onchange="previewImage(event)">

    <?php if (!empty($pengguna['gambar_profil']) && $pengguna['gambar_profil'] !== 'profile-user.png'): ?>
        <button type="submit" name="padam_gambar" value="1"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Anda pasti mahu padam gambar profil?');">
            Padam Gambar
        </button>
    <?php endif; ?>
</div>


            <div class="profile-info">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($pengguna['nama']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">No Matrik</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($pengguna['noMatrik']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Emel</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($pengguna['emel']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">No Telefon</label>
                    <input type="text" name="no_telefon" class="form-control" value="<?= htmlspecialchars($pengguna['no_telefon'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn-custom">KEMASKINI</button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const img = document.getElementById('preview-img');
        img.src = URL.createObjectURL(event.target.files[0]);
        img.onload = () => URL.revokeObjectURL(img.src); // Cleanup
    }
</script>

</body>
</html>
