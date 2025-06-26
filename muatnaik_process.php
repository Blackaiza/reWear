<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pakaian     = $_POST['namaPakaian'] ?? '';
    $jenama           = $_POST['jenama'] ?? '';
    $saiz             = $_POST['saiz'] ?? '';
    $kategori         = $_POST['kategori'] ?? '';
    $status           = $_POST['status'] ?? '';
    $jenis_pemberian  = $_POST['jenis_pemberian'] ?? '';
    $deskripsi        = $_POST['deskripsi'] ?? '';
    $no_matrik        = $_SESSION['no_matrik'];
    $gambar_paths = [];
    $gambar_hash_set = [];

    // Proses gambar baru (untuk kedua-dua upload baru & edit)
    if (!empty($_FILES['gambar']['name'][0])) {
        foreach ($_FILES['gambar']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['gambar']['error'][$index] === 0) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($fileType, $allowedTypes)) {
                    echo "<script>alert('Jenis fail tidak dibenarkan. Hanya JPG, JPEG, PNG sahaja.'); window.history.back();</script>";
                    exit();
                }

                $fileSize = $_FILES['gambar']['size'][$index];
                $maxSize = 2 * 1024 * 1024; // 2MB
                if ($fileSize > $maxSize) {
                    echo "<script>alert('Fail terlalu besar. Maksimum saiz gambar adalah 2MB.'); window.history.back();</script>";
                    exit();
                }

                $fileHash = sha1_file($tmpName);

                if (in_array($fileHash, $gambar_hash_set)) {
                    echo "<script>alert('Gambar sama telah dimuat naik dalam sesi ini. Sila pilih gambar lain.'); window.history.back();</script>";
                    exit();
                }
                $gambar_hash_set[] = $fileHash;

                $gambar_dir = "uploads/";
                if (!is_dir($gambar_dir)) {
                    mkdir($gambar_dir, 0777, true);
                }

                $ext = pathinfo($_FILES['gambar']['name'][$index], PATHINFO_EXTENSION);
                $gambar_name = uniqid('img_', true) . '.' . $ext;
                $gambar_path = $gambar_dir . $gambar_name;

                if (move_uploaded_file($tmpName, $gambar_path)) {
                    $gambar_paths[] = $gambar_path;
                } else {
                    echo "<script>alert('Gagal muat naik gambar.'); window.history.back();</script>";
                    exit();
                }
            }
        }
    }

    $gambar_baru = implode(',', $gambar_paths);
    $gambar = $gambar_baru;

    if (isset($_GET['edit'])) {
        $product_id = $_GET['edit'];
        $gambar_lama_array = $_POST['gambar_lama'] ?? [];
        $gambar_lama = implode(',', $gambar_lama_array);

        $stmt = $conn->prepare("SELECT gambar FROM tbl_pakaian WHERE idPakaian = ?");
        $stmt->execute([$product_id]);
        $gambar_asal_array = explode(',', $stmt->fetchColumn());

        foreach ($gambar_asal_array as $g) {
            if (!in_array($g, $gambar_lama_array) && file_exists($g)) {
                unlink($g);
            }
        }

        if (!empty($gambar_baru) && !empty($gambar_lama)) {
            $gambar = $gambar_lama . ',' . $gambar_baru;
        } elseif (empty($gambar_baru)) {
            $gambar = $gambar_lama;
        }
    }

    // Wajib sekurang-kurangnya ada satu gambar
    if (empty($gambar)) {
        echo "<script>alert('Sila muat naik sekurang-kurangnya satu gambar.'); window.history.back();</script>";
        exit();
    }

    try {
        if (isset($_GET['edit'])) {
            $product_id = $_GET['edit'];
            $stmt = $conn->prepare("UPDATE tbl_pakaian SET namaPakaian = ?, jenama = ?, saiz = ?, kategori = ?, status = ?, jenis_pemberian = ?, deskripsi = ?, gambar = ? WHERE idPakaian = ?");
            $stmt->execute([$nama_pakaian, $jenama, $saiz, $kategori, $status, $jenis_pemberian, $deskripsi, $gambar, $product_id]);
            echo "<script>alert('Pakaian berjaya dikemaskini!'); window.location.href='senaraipakaian.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_pakaian (noMatrik, namaPakaian, jenama, saiz, kategori, status, jenis_pemberian, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$no_matrik, $nama_pakaian, $jenama, $saiz, $kategori, $status, $jenis_pemberian, $deskripsi, $gambar]);
            echo "<script>alert('Pakaian berjaya dimuat naik!'); window.location.href='senaraipakaian.php';</script>";
        }
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Ralat: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>
