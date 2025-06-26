<?php
session_start();
include 'database.php';
require 'mailer_config.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idPertukaran']) && isset($_POST['status'])) {
        $id = $_POST['idPertukaran'];
        $statusBaru = $_POST['status'];

        if (in_array($statusBaru, ['diterima', 'ditolak'])) {
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Kemas kini status pertukaran
                $stmt = $conn->prepare("UPDATE tbl_pertukaran SET status = :status WHERE idPertukaran = :id");
                $stmt->bindParam(':status', $statusBaru);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // Jika diterima, kemaskini status pakaian
                if ($statusBaru === 'diterima') {
                    $stmt = $conn->prepare("SELECT idPakaianTarget, idPakaianTukar FROM tbl_pertukaran WHERE idPertukaran = ?");
                    $stmt->execute([$id]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result) {
                        $stmt = $conn->prepare("UPDATE tbl_pakaian SET status = 'Tidak Tersedia' WHERE idPakaian IN (?, ?)");
                        $stmt->execute([$result['idPakaianTarget'], $result['idPakaianTukar']]);
                    }
                }

                // Ambil semua data pertukaran lengkap
                $stmt = $conn->prepare("SELECT pt.noMatrikPeminta, u.emel, u.nama AS namaPeminta, 
                           p.namaPakaian AS namaTarget, p.saiz AS saizTarget, p.gambar AS gambarTarget,
                           p2.namaPakaian AS namaTukar, p2.saiz AS saizTukar, p2.gambar AS gambarTukar
                    FROM tbl_pertukaran pt
                    JOIN tbl_pengguna u ON pt.noMatrikPeminta = u.noMatrik
                    JOIN tbl_pakaian p ON pt.idPakaianTarget = p.idPakaian
                    JOIN tbl_pakaian p2 ON pt.idPakaianTukar = p2.idPakaian
                    WHERE pt.idPertukaran = ?");
                $stmt->execute([$id]);
                $info = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($info) {
                    $mail->addAddress($info['emel'], $info['namaPeminta']);
                    $mail->isHTML(true);

                    // Proses gambar target (ambil gambar pertama sahaja)
                    $gambarArrayTarget = explode(',', $info['gambarTarget']);
                    $gambarUtamaTarget = trim($gambarArrayTarget[0]);
                    $gambarTargetPath = !empty($gambarUtamaTarget) ? 
                        (str_starts_with($gambarUtamaTarget, 'uploads/') ? $gambarUtamaTarget : 'uploads/' . $gambarUtamaTarget) 
                        : 'no_image.jpg';

                    // Proses gambar tukar
                    $gambarArrayTukar = explode(',', $info['gambarTukar']);
                    $gambarUtamaTukar = trim($gambarArrayTukar[0]);
                    $gambarTukarPath = !empty($gambarUtamaTukar) ? 
                        (str_starts_with($gambarUtamaTukar, 'uploads/') ? $gambarUtamaTukar : 'uploads/' . $gambarUtamaTukar) 
                        : 'no_image.jpg';

                    if (file_exists($gambarTargetPath)) {
                        $mail->addEmbeddedImage($gambarTargetPath, 'pakaianTarget');
                    }
                    if (file_exists($gambarTukarPath)) {
                        $mail->addEmbeddedImage($gambarTukarPath, 'pakaianTukar');
                    }

                    $statusText = $statusBaru === 'diterima' ? '<span style="color:green;font-weight:bold;">DITERIMA</span>' : '<span style="color:red;font-weight:bold;">DITOLAK</span>';
                    $tajuk = $statusBaru === 'diterima' ? 'Permintaan Pertukaran Pakaian Diterima' : 'Harap Maaf, permintaan pertukaran pakaian telah ditolak';
                    $mail->Subject = "Status Permintaan Pertukaran Pakaian Anda";

                    $mail->Body = '
                    <div style="font-family:Poppins,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;border-radius:10px">
                        <div style="background:#3c7962;color:white;padding:15px;text-align:center;border-radius:10px 10px 0 0;">
                            <h2>'.$tajuk.'</h2>
                        </div>
                        <div style="background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;">
                            <p>Hai <b>'.$info['namaPeminta'].'</b>,</p>
                            <p>Permintaan anda telah '.$statusText.' oleh pemilik pakaian.</p>
                            <div style="border:1px solid #ccc;padding:20px;border-radius:8px;margin:20px 0;">
                                <p><b>Pakaian Diminta:</b> '.$info['namaTarget'].' ('.$info['saizTarget'].')</p>
                                <p><img src="cid:pakaianTarget" width="150"></p>
                                <p><b>Pakaian Ditukarkan:</b> '.$info['namaTukar'].' ('.$info['saizTukar'].')</p>
                                <p><img src="cid:pakaianTukar" width="150"></p>
                                <div style="margin-top:20px;text-align:center;">
                                    <a href="https://amalia.aizathami.website/pemberian_percuma.php?tab=permintaan" style="background:#3c7962;color:white;padding:12px 25px;border-radius:50px;text-decoration:none;font-weight:bold;">Semak Permintaan</a>
                                    <p>Sila Klik Butang <b>Semak Permintaan</b> untuk log masuk ke ReWear.</p>
                                </div>
                            </div>
                            <p style="font-size:12px;color:#999;text-align:center;margin-top:30px;">Email ini dihantar secara automatik. Sila jangan balas email ini.</p>
                        </div>
                    </div>';

                    $mail->send();
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'status' => $statusBaru
                ]);
                exit();

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Status tidak sah.']);
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
        exit();
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak sah.']);
    exit();
}
?>
