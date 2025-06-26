<?php
session_start();
include 'database.php';
require 'mailer_config.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idPermohonan']) && isset($_POST['status'])) {
        $idPermohonan = $_POST['idPermohonan'];
        $statusBaru = $_POST['status'];
        $noMatrikPemilik = $_SESSION['no_matrik'];

        if (in_array($statusBaru, ['Diterima', 'Ditolak'])) {
            try {
                $stmt = $conn->prepare("SELECT pp.*, u.emel, u.nama AS namaPeminta, p.namaPakaian, p.saiz, p.gambar 
                                         FROM tbl_percuma pp 
                                         JOIN tbl_pengguna u ON pp.matrikPeminta = u.noMatrik 
                                         JOIN tbl_pakaian p ON pp.idPakaian = p.idPakaian 
                                         WHERE pp.idPercuma = ? AND pp.noMatrikPemilik = ?");
                $stmt->execute([$idPermohonan, $noMatrikPemilik]);
                $permohonan = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($permohonan && $permohonan['status'] === 'Menunggu') {
                    $update = $conn->prepare("UPDATE tbl_percuma SET status = ? WHERE idPercuma = ?");
                    $update->execute([$statusBaru, $idPermohonan]);

                    $gambarArray = explode(',', $permohonan['gambar']);
                    $gambarUtama = trim($gambarArray[0]);
                    $gambarPath = !empty($gambarUtama) ? (str_starts_with($gambarUtama, 'uploads/') ? $gambarUtama : 'uploads/' . $gambarUtama) : 'no_image.jpg';

                    $mail->addAddress($permohonan['emel'], $permohonan['namaPeminta']);
                    $mail->isHTML(true);

                    if (file_exists($gambarPath)) {
                        $mail->addEmbeddedImage($gambarPath, 'pakaianGambar');
                    }

                    $tajuk = $statusBaru === 'Diterima' ? 'Permintaan Pakaian Percuma Anda telah Diterima' : 'Harap Maaf permintaan Pakaian Percuma Anda Ditolak';
                    $warna = $statusBaru === 'Diterima' ? 'green' : 'red';

                    $mail->Subject = "Status Permintaan Pakaian Percuma";
                    $mail->Body = '
                    <div style="font-family:Poppins,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;border-radius:10px">
                        <div style="background:#3c7962;color:white;padding:15px;text-align:center;border-radius:10px 10px 0 0;">
                            <h2>'.$tajuk.'</h2>
                        </div>
                        <div style="background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;">
                            <p>Hai <b>'.$permohonan['namaPeminta'].'</b>,</p>
                            <p>Permintaan pakaian percuma anda telah <span style="color:'.$warna.';font-weight:bold;">'.$statusBaru.'</span> oleh pemilik pakaian.</p>
                            <div style="border:1px solid #ccc;padding:20px;border-radius:8px;margin:20px 0;">
                                <p><b>Pakaian:</b> '.$permohonan['namaPakaian'].' ('.$permohonan['saiz'].')</p>
                                <p><img src="cid:pakaianGambar" width="150"></p>
                                <div style="margin-top:20px;text-align:center;">
                                    <a href="https://amalia.aizathami.website/pemberian_percuma.php?tab=permintaan" style="background:#3c7962;color:white;padding:12px 25px;border-radius:50px;text-decoration:none;font-weight:bold;">Semak Permohonan</a>
                                     <p>Sila Klik Butang <b>Semak Permintaan</b> untuk log masuk ke ReWear.</p>
                                </div>
                            </div>
                            <p style="font-size:12px;color:#999;text-align:center;margin-top:30px;">Email ini dihantar secara automatik. Sila jangan balas email ini.</p>
                        </div>
                    </div>';

                    $mail->send();
                }

                header("Location: pemberian_percuma.php?tab=permohonan");
                exit();

            } catch (PDOException $e) {
                echo "Ralat: " . $e->getMessage();
                exit();
            }
        } else {
            echo "Status tidak sah.";
            exit();
        }
    }
} else {
    header("Location: pemberian_percuma.php");
    exit();
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmAction(status, id) {
  Swal.fire({
    title: 'Anda pasti?',
    text: "Permohonan ini akan " + status.toUpperCase() + ".",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3c7962',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, teruskan!'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '';
      form.innerHTML = `<input type="hidden" name="idPermohonan" value="${id}">
                        <input type="hidden" name="status" value="${status}">`;
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>
