<?php
include 'database.php';
session_start();

// DB connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['login'])) {
    $noMatrik = trim(strtoupper($_POST['no_matrik']));
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE noMatrik = :noMatrik");
        $stmt->bindParam(':noMatrik', $noMatrik, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === trim($user['katalaluan'])) {
            $_SESSION['no_matrik'] = $user['noMatrik'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_level'] = $user['user_level'];

            if ($user['user_level'] === 'pentadbir') {
                header("Location: index.php");
            } else {
                header("Location: indexnu.php");
            }
            exit();
        } else {
            $error = "No Matrik atau Kata Laluan salah!";
        }
    } catch (PDOException $e) {
        $error = "Ralat: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReWear - Log Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('mukautama.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(5, 76, 85, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.75);
            display: flex;
            flex-direction: row;
        }
        .gradient-left {
            background: linear-gradient(135deg, #3c7962, #a6d3c2);
            color: #fff;
            padding: 40px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .gradient-left h4 {
            font-weight: 700;
            margin-bottom: 15px;
        }
        .login-section {
            width: 50%;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
        }
        .btn-custom {
            background: #3c7962;
            color: #fff;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: 0.3s ease;
        }
        .btn-custom:hover {
            background: #2C4230;
        }
        .form-control {
            border-radius: 50px;
            padding: 12px 20px;
        }
        .form-check-input:checked {
            background-color: #3c7962;
            border-color: #3c7962;
        }
        .extra-links {
            font-size: 0.9rem;
            display: flex;
            justify-content: space-between;
        }
        .extra-links a {
            color: #3c7962;
            text-decoration: none;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .gradient-left {
                display: none;
            }
            .login-section {
                width: 100%;
            }
            .card {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="gradient-left">
        <h4>Selamat Datang ke ReWear</h4>
        <p>Platform pertukaran pakaian komuniti UKM.</p>
    </div>

    <div class="login-section">
        <div class="text-center mb-4">
            <img src="logorewear.png" alt="Logo" style="width: 130px;">
            <h5 class="mt-3 fw-bold">Portal Log Masuk</h5>
        </div>
        <form method="post">
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger"> <?php echo $error; ?> </div>
            <?php } ?>
            <div class="mb-3">
                <label for="no_matrik" class="form-label">No Matrik</label>
                <input type="text" class="form-control" id="no_matrik" name="no_matrik" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata Laluan</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="showPassword" onclick="togglePassword()">
                <label class="form-check-label" for="showPassword">Tunjukkan Kata Laluan</label>
            </div>
            <div class="extra-links mb-3">
                <a href="daftar.php">Daftar Akaun</a>
                <a href="lupa_katalaluan.php">Lupa Kata Laluan?</a>
            </div>
            <div class="text-center">
                <button type="submit" name="login" class="btn btn-custom w-100">Log Masuk</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>

</body>
</html>
