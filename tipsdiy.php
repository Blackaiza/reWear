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

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function extractYoutubeId($url) {
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return $matches[1];
    }
    return null;
}

// Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $tajuk = $_POST['tajuk'];
    $bahan = $_POST['bahan'];
    $langkah = $_POST['langkah'];
    $youtube = extractYoutubeId($_POST['youtube'] ?? '');

    if ($id) {
        $stmt = $conn->prepare("UPDATE tbl_tipsdiy SET tajuk = ?, bahan = ?, langkah = ?, youtube = ? WHERE id = ?");
$stmt->execute([$tajuk, $bahan, $langkah, $youtube, $id]);

    } else {
        $stmt = $conn->prepare("INSERT INTO tbl_tipsdiy (tajuk, bahan, langkah, youtube) VALUES (?, ?, ?, ?)");
$stmt->execute([$tajuk, $bahan, $langkah, $youtube]);

    }
    header("Location: tipsdiy.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM tbl_tipsdiy WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: tipsdiy.php");
    exit();
}

// Fetch all
$stmt = $conn->prepare("SELECT * FROM tbl_tipsdiy ORDER BY id DESC");
$stmt->execute();
$allTips = $stmt->fetchAll();

// Embed YouTube
function embedYoutube($text) {
    return preg_replace(
        '/https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
        '<iframe width="300" height="200" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
        $text
    );
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>ReWear: Tips DIY</title>
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
            background-color: #f9f9f9;
        }
        .container-custom {
            padding: 40px;
        }
        h2 {
            font-weight: 700;
            color: #3c7962;
            margin-bottom: 30px;
        }
        textarea {
            border-radius: 10px;
            min-height: 100px;
        }
        .btn-success {
            background-color: #3c7962;
            border: none;
        }
        .btn-success:hover {
            background-color: #295c47;
        }
        .table th {
            background-color: #a7c4a2;
            color: black;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .icon-btn {
            border: none;
            background: none;
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
    <h2 class="text-center">Tips Diy</h2>

    <form method="POST" class="mb-4">
    <input type="hidden" name="id" value="<?= $_GET['edit'] ?? '' ?>">

    <textarea name="tajuk" class="form-control mb-2" placeholder="Tajuk" required><?= $_GET['tajuk'] ?? '' ?></textarea>

    <textarea name="bahan" class="form-control mb-2" placeholder="Senarai bahan" rows="3" required><?= $_GET['bahan'] ?? '' ?></textarea>

    <textarea name="langkah" class="form-control mb-2" placeholder="Langkah-langkah" rows="5" required><?= $_GET['langkah'] ?? '' ?></textarea>

    <?php
$youtubeVal = '';
if (isset($_GET['youtube']) && $_GET['youtube'] !== '') {
    $youtubeVal = 'https://www.youtube.com/watch?v=' . htmlspecialchars($_GET['youtube']);
}
?>
<input name="youtube" class="form-control mb-3" type="url" placeholder="Link YouTube (Jika Ada)" value="<?= $youtubeVal ?>">


    <button type="submit" class="btn btn-success">
        <?= isset($_GET['edit']) ? 'Kemaskini' : 'Muat Naik' ?>
    </button>
</form>


    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Tajuk</th>
                <th>Kandungan</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allTips as $tip): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($tip['tajuk']) ?></strong></td>
                    <td>
                    <?php

                        // YouTube video
$video = !empty($tip['youtube']) 
? '<iframe width="300" height="200" src="https://www.youtube.com/embed/' . htmlspecialchars($tip['youtube']) . '" frameborder="0" allowfullscreen></iframe>'
: '';

// Papar bahan & langkah
echo $video;
echo '<div class="text-start mt-3">';
echo '<strong>Bahan Diperlukan:</strong><br>' . nl2br(htmlspecialchars($tip['bahan'])) . '<br><br>';
echo '<strong>Langkah-langkah:</strong><br>' . nl2br(htmlspecialchars($tip['langkah']));
echo '</div>';


                    ?>
                </td>
                    <td>
                        <a href="?edit=<?= $tip['id'] ?>&tajuk=<?= urlencode($tip['tajuk']) ?>&bahan=<?= urlencode($tip['bahan']) ?>&langkah=<?= urlencode($tip['langkah']) ?>&youtube=<?= urlencode($tip['youtube']) ?>"
                        class="icon-btn" title="Kemaskini">
                            <i class="fas fa-edit text-primary me-2"></i>
                        </a>
                        <a href="?delete=<?= $tip['id'] ?>" onclick="return confirm('Padam tips ini?');" class="icon-btn" title="Padam">
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<!-- Bootstrap Bundle JS + Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
