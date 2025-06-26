<?php
include 'database.php';
session_start();

date_default_timezone_set('Asia/Kuala_Lumpur');

// Include PHPMailer manually
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emel = $_POST['emel'];

    // **Validate email format (Only @siswa.ukm.edu.my & @ukm.edu.my)**
    if (!preg_match('/@(siswa\.ukm\.edu\.my|ukm\.edu\.my)$/', $emel)) {
        echo "<script>alert('Email tidak sah! Sila guna email @siswa.ukm.edu.my atau @ukm.edu.my sahaja.'); window.location.href='lupa_katalaluan.php';</script>";
        exit();
    }

    // Semak jika email wujud dalam database
    $stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE emel = ?");
    $stmt->execute([$emel]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Cipta token unik
        $token = bin2hex(random_bytes(50)); 
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes")); // Luput dalam 30 minit

        // Simpan token dalam database
        $stmt = $conn->prepare("UPDATE tbl_pengguna SET reset_token=?, reset_expiry=? WHERE emel=?");
        $stmt->execute([$token, $expiry, $emel]);

        // Hantar email reset password guna PHPMailer
        $reset_link = "http://amalia.aizathami.website/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Tukar jika guna provider lain
            $mail->SMTPAuth = true;
            $mail->Username = 'noramalia22@gmail.com'; // Tukar kepada email anda
            $mail->Password = 'nhdt mzng cfqa whsb'; ; // ✅ Guna App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Tetapan email
            $mail->setFrom('noramalia22@gmail.com', 'ReWear Support');
            $mail->addAddress($emel);
            $mail->Subject = "Reset Kata Laluan";
            $mail->Body = "Klik link berikut untuk reset kata laluan: " . $reset_link;

            $mail->send();
            echo "<script>alert('Sila semak email anda untuk reset kata laluan!'); window.location.href='login.php';</script>";
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Email gagal dihantar: " . $mail->ErrorInfo . "');</script>";
        }
    } else {
        echo "<script>alert('Email tidak dijumpai dalam sistem!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Laluan</title>
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
            max-width: 500px;  /* Bigger container */
            width: 100%;
            background: #ffffff;
            padding: 40px;  /* More space inside */
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
        <h2 class="mb-3" style="color: #3c7962;">Lupa Kata Laluan</h2>
        <p>Masukkan emel UKM anda untuk menerima pautan set semula kata laluan.</p>
        <form method="post">
            <div class="mb-3">
                <input type="email" id="emel" name="emel" class="form-control" placeholder="contoh@siswa.ukm.edu.my" required>
            </div>
            <button type="submit" class="btn-custom">Hantar</button>
        </form>
        <a href="login.php" class="login-link">Kembali ke Log Masuk</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
