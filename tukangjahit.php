<?php
session_start(); // Start the session
if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_level'] !== 'pentadbir') {
    header("Location: indexnu.php");
    exit();
}
include 'database.php';

// Database connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Insert or update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telefon = $_POST['telefon'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE tbl_tukangjahit SET nama = ?, alamat = ?, telefon = ? WHERE id = ?");
        $stmt->execute([$nama, $alamat, $telefon, $id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO tbl_tukangjahit (nama, alamat, telefon) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $alamat, $telefon]);
    }

    header("Location:tukangjahit.php");
    exit();
}

// Delete logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tbl_tukangjahit WHERE id = ?");
    $stmt->execute([$id]);
    header("Location:tukangjahit.php");
    exit();
}

// Fetch all
$stmt = $conn->prepare("SELECT * FROM tbl_tukangjahit ORDER BY id DESC");
$stmt->execute();
$tukangJahitList = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>ReWear: Tukang Jahit</title>
     <!-- Bootstrap -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }
        .container-custom {
            padding: 40px;
        }
        h2 {
            font-weight: 700;
            color: #3c7962;
            margin-bottom: 30px;
        }
        .btn-custom {
            background-color: #88b77b;
            color: white;
            border-radius: 20px;
            padding: 6px 20px;
        }
        .btn-custom:hover {
            background-color: #3c7962;
        }
        .form-control {
            border-radius: 20px;
        }
        table th {
            background-color: #a7c4a2;
            color: black;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .icon-btn {
            border: none;
            background: none;
            color: #000;
            font-size: 1.2rem;
        }
        .icon-btn:hover {
            color: #3c7962;
        }
    </style>
</head>
<body>

<?php include 'nav_bar.php'; ?>

<div class="container container-custom">
    <div class="row">
        <!-- Form -->
        <div class="col-md-4">
            <h2>Tukang Jahit</h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $_GET['edit'] ?? '' ?>">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" name="nama" value="<?= $_GET['nama'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input type="text" class="form-control" name="alamat" value="<?= $_GET['alamat'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No-Telefon</label>
                    <input type="text" class="form-control" name="telefon" value="<?= $_GET['telefon'] ?? '' ?>" required>
                </div>
                <button type="submit" class="btn btn-custom"><?= isset($_GET['edit']) ? 'Kemaskini' : 'Simpan' ?></button>
            </form>
        </div>

        <!-- Table -->
        <div class="col-md-8">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No-Telefon</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tukangJahitList as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['telefon']) ?></td>
                            <td>
                                <a href="?edit=<?= $row['id'] ?>&nama=<?= urlencode($row['nama']) ?>&alamat=<?= urlencode($row['alamat']) ?>&telefon=<?= urlencode($row['telefon']) ?>" class="icon-btn" title="Kemaskini"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Padam data ini?');" class="icon-btn" title="Padam"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Bootstrap Bundle JS + Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
