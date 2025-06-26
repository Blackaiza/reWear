<?php
include 'database.php';
session_start();

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Semak token dalam database
    $stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('Token tidak sah atau telah tamat!'); window.location.href='login.php';</script>";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['katalaluan'];  // ⚠ PASSWORD NOT HASHED

        // Kemaskini password dalam database (tanpa hashing)
        $stmt = $conn->prepare("UPDATE tbl_pengguna SET katalaluan = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?");
        $stmt->execute([$new_password, $token]);

        echo "<script>alert('Kata laluan berjaya ditukar! Sila log masuk.'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Token tidak sah!'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Laluan</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('mukautama.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;  
            width: 100%;
            background: #ffffff;
            padding: 40px;  
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }


        .form-label {
            font-weight: 500;
            color: #3c7962;
        }

        .form-control {
            border: 1px solid #3c7962;
            border-radius: 5px;
            width: 100%;
        }

        .btn-custom {
            background: #3c7962;
            color: #fff;
            font-weight: 600;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-custom:hover {
            background: #2C4230;
        }

        .login-link {
            margin-top: 10px;
            display: block;
            color: #3c7962;
            font-size: 14px;
        }

    </style>
</head>
<body>

    <div class="container">
        <h2 class="mb-3" style="color: #3c7962;">Reset Kata Laluan</h2>
        <p>Masukkan kata laluan baru anda.</p>
        <form method="post">
            <div class="mb-3">
                <input type="password" id="katalaluan" name="katalaluan" class="form-control" placeholder="Masukkan kata laluan baru" required>
            </div>
            <button type="submit" class="btn-custom">Reset Kata Laluan</button>
        </form>
        <a href="login.php" class="login-link">Kembali ke Log Masuk</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
