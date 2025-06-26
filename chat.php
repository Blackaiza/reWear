<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}
// Database connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pakaianId = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tbl_pakaian WHERE idPakaian = ?");
$stmt->execute([$pakaianId]);
$item = $stmt->fetch();

$receiver_id = $item['noMatrik'];

// Dapatkan maklumat pemilik dari tbl_pengguna
$stmt2 = $conn->prepare("SELECT * FROM tbl_pengguna WHERE noMatrik = ?");
$stmt2->execute([$receiver_id]);
$pemilik = $stmt2->fetch();

$namaPemilik = $pemilik['nama'] ?? 'Pemilik';

// Guna path penuh yang disimpan dalam DB
$gambarProfilPath = $pemilik['gambar_profil'] ?? '';

// Semak sama ada fail wujud (elak broken image)
$gambarPemilik = (!empty($gambarProfilPath) && file_exists(__DIR__ . '/' . $gambarProfilPath))
    ? $gambarProfilPath
    : 'profile-user.png';



$sender_id = $_SESSION['no_matrik'];
$receiver_id = $item['noMatrik'];
$gambarPertama = explode(',', $item['gambar'])[0];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReWear: Mesej Pemilik</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/3b7d808c92.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f2f2f2;
        }

        .chat-wrapper {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .chat-header img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 15px;
        }

        .chat-header h5 {
            margin: 0;
            color: #3c7962;
            font-weight: bold;
        }

        .chat-header small {
            color: #777;
        }

        .back-btn {
            font-size: 1.2rem;
            color: #3c7962;
            text-decoration: none;
            margin-right: 15px;
        }

        .back-btn:hover {
            color: #2C4230;
        }

        .chat-box {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bubble {
            padding: 12px 18px;
            border-radius: 20px;
            max-width: 70%;
            font-size: 0.95rem;
        }

        .bubble-left {
            background-color: #f0f0f0;
            align-self: flex-start;
        }

        .bubble-right {
            background-color: #3c7962;
            color: white;
            align-self: flex-end;
        }

        form input[type="text"] {
            border-radius: 30px;
            padding: 10px 20px;
            border: 1px solid #ccc;
        }

        .btn-send {
            border-radius: 30px;
            padding: 10px 20px;
            background-color: #3c7962;
            color: white;
            font-weight: bold;
            border: none;
        }

        .btn-send:hover {
            background-color: #2C4230;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        .rounded-circle {
        border: 2px solid #3c7962;
        padding: 2px;
    }

    </style>
</head>
<body>

<div class="chat-wrapper">

    <!-- Header: Info Pemilik -->
    <div class="chat-header d-flex align-items-center">
        <a href="detail.php?id=<?php echo $item['idPakaian']; ?>" class="back-btn me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <img src="<?php echo $gambarPemilik; ?>"
        class="rounded-circle me-3" width="60" height="60" style="object-fit:cover;">

        <h5 class="mb-0"><?php echo $namaPemilik; ?></h5>
    </div>

    <!-- Produk Pakaian -->
    <div class="text-center mb-4 mt-4">
        <img src="<?php echo $gambarPertama; ?>" alt="Gambar Pakaian" 
             style="width: 120px; height: 120px; object-fit: cover; border-radius: 10px;">
        <div class="mt-2">
            <h5 class="mb-1" style="color: #3c7962;"><?php echo $item['namaPakaian']; ?></h5>
            <small class="text-muted">Anda sedang bertanya tentang pakaian ini</small>
        </div>
    </div>


    <div class="chat-box">
        <?php
        $stmt = $conn->prepare("SELECT * FROM tbl_komunikasi 
            WHERE idPakaian = ? AND ((penghantar = ? AND penerima = ?) OR (penghantar = ? AND penerima = ?)) 
            ORDER BY tarikhMesej ASC");
        $stmt->execute([$pakaianId, $sender_id, $receiver_id, $receiver_id, $sender_id]);
        $messages = $stmt->fetchAll();

        foreach ($messages as $msg):
            $own = $msg['penghantar'] == $sender_id;
            $class = $own ? 'bubble-right' : 'bubble-left';
        ?>
            <div class="bubble <?php echo $class; ?>">
                <?php echo htmlspecialchars($msg['kandunganMesej']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="send_message.php" method="POST" class="d-flex">
        <input type="hidden" name="pakaian_id" value="<?php echo $pakaianId; ?>">
        <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
        <input type="text" name="content" class="form-control me-2" placeholder="Tulis mesej..." required>
        <button type="submit" class="btn btn-send">HANTAR</button>
    </form>
</div>

</body>
</html>
