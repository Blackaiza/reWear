<?php
include 'database.php';
session_start();
require 'mailer_config.php'; // PHPMailer config

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $emel = trim($_POST['emel']);
    $no_matrik = strtoupper(trim($_POST['no_matrik']));
    $katalaluan = trim($_POST['katalaluan']);
    
    $jenis_pengguna = 'warga UKM';

    // Email validation
    if (!preg_match("/^[^@]+@((siswa\.ukm\.edu\.my)|(ukm\.edu\.my))$/", $emel)) {
        echo "<script>alert('Hanya emel UKM sahaja dibenarkan untuk pendaftaran.');</script>";
    } 
    // Matrik validation
    elseif (!preg_match("/^[AK]\d{6}$/", $no_matrik)) {
        echo "<script>alert('No Matrik mestilah dalam format A123456 atau K123456.');</script>";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE emel = ?");
        $stmt->execute([$emel]);
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Emel telah didaftarkan!');</script>";
        }
        // Check if no_matrik already exists
        else {
            $stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE noMatrik = ?");
            $stmt->execute([$no_matrik]);
            if ($stmt->rowCount() > 0) {
                echo "<script>alert('No Matrik telah didaftarkan!');</script>";
            } else {
                // Proceed to insert the new user if email and no_matrik are unique
                try {
                    $stmt = $conn->prepare("INSERT INTO tbl_pengguna (nama, emel, noMatrik, katalaluan, user_level) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nama, $emel, $no_matrik, $katalaluan, $jenis_pengguna]);

                    // Send confirmation email after successful registration
                    $mail->addAddress($emel, $nama);
                    $mail->Subject = 'Pendaftaran Berjaya - ReWear';
                    $mail->isHTML(true);
                    $mail->Body = '
                    <div style="font-family:Poppins,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;border-radius:10px">
                        <div style="background:#3c7962;color:white;padding:15px;text-align:center;border-radius:10px 10px 0 0;">
                            <h2 style="margin:0;">Pendaftaran Berjaya!</h2>
                        </div>
                        <div style="background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;">
                            <p>Hai <b>'.$nama.'</b>,</p>
                            <p>Terima kasih kerana mendaftar di sistem <b>ReWear</b>. Akaun anda telah berjaya didaftarkan.</p>
                            <div style="border:1px solid #ccc;padding:20px;border-radius:8px;margin:20px 0;">
                                <h4 style="margin-top:0;">Maklumat Anda:</h4>
                                <p><b>Nama:</b> '.$nama.'</p>
                                <p><b>No Matrik:</b> '.$no_matrik.'</p>
                                <p><b>Email:</b> '.$emel.'</p>
                            </div>
                            <p style="font-size:12px;color:#999;text-align:center;">Email ini dihantar secara automatik. Sila jangan balas email ini.</p>
                        </div>
                    </div>';

                    // Attempt to send the email
                    $mail->send();
                    echo "<script>alert('Pendaftaran berjaya! Emel pengesahan telah dihantar.'); window.location.href='login.php';</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Pendaftaran berjaya tetapi emel gagal dihantar.'); window.location.href='login.php';</script>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akaun</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('mukautama.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .container-custom {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .register-container {
            max-width: 500px;
            width: 100%;
            background: rgba(255,255,255,0.85);
            padding: 40px;
            border-radius: 15px;
            backdrop-filter: blur(5px);
            box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .form-control {
            background: #eef7f1;
            border: 1px solid #3c7962;
            border-radius: 50px;
            padding: 12px 20px;
            color: #3c7962;
            transition: 0.3s;
        }
        .form-control.is-invalid {
            border-color: red;
        }
        .form-control.is-valid {
            border-color: green;
        }
        input[type="email"]:valid,
        input[type="email"]:invalid,
        input[type="text"]:valid,
        input[type="text"]:invalid {
            box-shadow: none !important;
        }
        .btn-custom {
            background: #3c7962;
            color: #fff;
            border-radius: 50px;
            padding: 12px 20px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background: #2c594c;
        }
        h2 {
            font-weight: 700;
            color: #3c7962;
        }
        .login-link {
            margin-top: 20px;
            display: block;
            color: #3c7962;
            font-weight: 500;
        }
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .valid-icon {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.3rem;
        }
    </style>
</head>
<body>

<div class="container-custom">
    <div class="register-container">
        <h2>Daftar Akaun</h2>
        <form method="post" onsubmit="return validateForm()">
            <div class="mb-3 position-relative">
                <input type="text" name="nama" class="form-control" placeholder="Nama" required>
            </div>
            <div class="mb-3 position-relative">
                <input type="email" name="emel" id="emel" class="form-control" placeholder="Emel UKM" required onkeyup="liveValidateEmail()">
                <div id="emailError" class="error-message"></div>
                <span id="emailValidIcon" class="valid-icon"></span>
            </div>
            <div class="mb-3 position-relative">
                <input type="text" name="no_matrik" id="no_matrik" class="form-control" placeholder="No Matrik (A123456 atau K123456)" required onkeyup="liveValidateMatrik(); this.value = this.value.toUpperCase();">
                <div id="matrikError" class="error-message"></div>
                <span id="matrikValidIcon" class="valid-icon"></span>
            </div>
            <div class="mb-3 position-relative">
                <input type="password" name="katalaluan" class="form-control" placeholder="Kata Laluan" required>
            </div>
            <button type="submit" class="btn btn-custom w-100">Daftar</button>
        </form>
        <a href="login.php" class="login-link">Kembali ke Log Masuk</a>
    </div>
</div>

<script>
function validateForm() {
    return validateEmail() && validateMatrik();
}

function validateEmail() {
    var emailField = document.getElementById("emel").value.trim();
    var emailError = document.getElementById("emailError");
    var ukmPattern = /^[^@]+@((siswa\.ukm\.edu\.my)|(ukm\.edu\.my))$/;

    if (!ukmPattern.test(emailField)) {
        emailError.textContent = "Hanya emel UKM sahaja dibenarkan.";
        return false;
    } else {
        emailError.textContent = "";
        return true;
    }
}

function validateMatrik() {
    var matrikField = document.getElementById("no_matrik").value.trim();
    var matrikError = document.getElementById("matrikError");
    var matrikPattern = /^[AK]\d{6}$/;

    if (!matrikPattern.test(matrikField)) {
        matrikError.textContent = "No Matrik mestilah dalam format A123456";
        return false;
    } else {
        matrikError.textContent = "";
        return true;
    }
}

function liveValidateEmail() {
    var emailField = document.getElementById("emel");
    var emailError = document.getElementById("emailError");
    var emailValidIcon = document.getElementById("emailValidIcon");
    var ukmPattern = /^[^@]+@((siswa\.ukm\.edu\.my)|(ukm\.edu\.my))$/;

    if (emailField.value.trim() === "") {
        emailError.textContent = "";
        emailField.classList.remove("is-valid", "is-invalid");
        emailValidIcon.innerHTML = "";
        return;
    }

    if (!ukmPattern.test(emailField.value.trim())) {
        emailError.textContent = "Sila gunakan emel UKM.";
        emailField.classList.add("is-invalid");
        emailField.classList.remove("is-valid");
        emailValidIcon.style.color = "red";
    } else {
        emailError.textContent = "";
        emailField.classList.add("is-valid");
        emailField.classList.remove("is-invalid");     
        emailValidIcon.style.color = "green";
    }
}

function liveValidateMatrik() {
    var matrikField = document.getElementById("no_matrik");
    var matrikError = document.getElementById("matrikError");
    var matrikValidIcon = document.getElementById("matrikValidIcon");
    var matrikPattern = /^[AK]\d{6}$/;

    if (matrikField.value.trim() === "") {
        matrikError.textContent = "";
        matrikField.classList.remove("is-valid", "is-invalid");
        matrikValidIcon.innerHTML = "";
        return;
    }

    if (!matrikPattern.test(matrikField.value.trim())) {
        matrikError.textContent = "No Matrik mestilah dalam format A123456.";
        matrikField.classList.add("is-invalid");
        matrikField.classList.remove("is-valid");
        matrikValidIcon.style.color = "red";
    } else {
        matrikError.textContent = "";
        matrikField.classList.add("is-valid");
        matrikField.classList.remove("is-invalid");
        matrikValidIcon.style.color = "green";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
