<?php
session_start();
include 'database.php';

// Ensure user is logged in
if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

// Settings
$itemsPerPage = 4;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// DB connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get total count for pagination
$countStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_pakaian WHERE noMatrik = ?");
$countStmt->execute([$_SESSION['no_matrik']]);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch limited clothing items
$stmt = $conn->prepare("SELECT * FROM tbl_pakaian WHERE noMatrik = ? ORDER BY tarikh_muatnaik DESC LIMIT ? OFFSET ?");
$stmt->bindParam(1, $_SESSION['no_matrik']);
$stmt->bindParam(2, $itemsPerPage, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();
?>

<!-- HTML Starts -->
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReWear: Senarai Pakaian</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

     <!-- Bootstrap Bundle with Popper -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('bg.jpg') center fixed;
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .title-bold {
            font-weight: 700;
            text-align: center;
            color: #3c7962;
            margin-bottom: 20px;
        }

        .card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-left: 15px;
            border-radius: 8px;
        }

        .card-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .card-content p {
            margin: 5px 0;
        }

        .card-footer {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-custom {
            background: #3c7962;
            color: #fff;
            font-weight: 600;
            padding: 10px 15px;
            text-align: center;
        }

        .btn-custom:hover {
            background: #2C4230;
        }

        .btn-delete {
            background: #d9534f;
            color: #fff;
            padding: 10px 15px;
        }

        .btn-delete:hover {
            background: #c9302c;
        }

        .card-body {
            display: flex;
            flex-direction: row;
            gap: 5px;
            align-items: center;
            width: 100%;
        }

        .card-body .text {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex-grow: 1;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000; /* Pastikan navbar berada di atas kandungan lain */
            padding-top: 0px; /* Optional, adjust as needed */
        }
 
        .card-title {
    color: #3c7962;
    font-weight: 600;
}

.btn-custom {
    background-color: #3c7962;
    color: white !important;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    text-decoration: none !important;
    display: inline-block;
    transition: 0.3s;
}

.btn-custom:hover {
    background-color: #2c5948;
}

.btn-delete {
    background-color: #dc3545;
    color: white !important;
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    text-decoration: none !important;
    display: inline-block;
    transition: 0.3s;
}

.btn-delete:hover {
    background-color: #b02a37;
}
.btn-pagination-active {
    background-color:rgb(99, 153, 132);
    color: white !important;
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    text-decoration: none !important;
    display: inline-block;
    transition: 0.3s;
}

.btn-pagination-active:hover {
    background-color: #2c5948;
}


    </style>
</head>

<body>
<?php include 'nav_bar_nu.php'; ?>

<div class="container">
    <h2 class="title-bold mb-4">Senarai Pakaian Anda</h2>

    <div class="row">
        <?php foreach ($items as $item): ?>
            <div class="col-md-6 col-12 mb-4">
                <div class="card h-100 border-0 shadow-sm p-3 d-flex flex-row align-items-center">
                    <?php
                        $gambarArray = explode(',', $item['gambar']);
                        $gambarPertama = $gambarArray[0];
                    ?>
                    <img src="<?php echo $gambarPertama; ?>" alt="Image" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">

                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1"><?php echo $item['namaPakaian']; ?></h5>
                        <p class="mb-1"><strong>Jenama:</strong> <?php echo $item['jenama']; ?></p>
                        <p class="mb-2"><strong>Status:</strong> <?php echo $item['status']; ?></p>

                        <div class="d-flex gap-2">
                            <a href="muatnaik_form.php?edit=<?php echo $item['idPakaian']; ?>" class="btn-custom">Lihat Pakaian</a>
                            <a href="delete_pakaian.php?id=<?php echo $item['idPakaian']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this item?')">Hapus</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- PAGINATION -->
    <div class="text-center mt-4">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline-secondary me-2">← Sebelumnya</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'btn-pagination-active' : 'btn btn-outline-secondary'; ?> mx-1"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline-secondary ms-2">Seterusnya →</a>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <a href="muatnaik_form.php" class="btn btn-outline-success">+ Muat Naik Pakaian Baru</a>
    </div>
</div>

</body>
</html>
