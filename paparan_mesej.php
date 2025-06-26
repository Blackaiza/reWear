<?php
session_start();
include 'database.php';

if (!isset($_SESSION['no_matrik'])) {
    header("Location: login.php");
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$no_matrik = $_SESSION['no_matrik'];
$selected_receiver = $_GET['chat_with'] ?? null;
$selected_pakaian = $_GET['idPakaian'] ?? null;

if ($selected_receiver === $no_matrik) {
    echo "Anda tidak boleh mesej diri sendiri.";
    exit();
}

$receiverInfo = null;
if ($selected_receiver) {
    $stmt = $conn->prepare("SELECT * FROM tbl_pengguna WHERE noMatrik = ?");
    $stmt->execute([$selected_receiver]);
    $receiverInfo = $stmt->fetch();
    $gambarReceiver = (!empty($receiverInfo['gambar_profil']) && file_exists($receiverInfo['gambar_profil']))
        ? $receiverInfo['gambar_profil']
        : 'profile-user.png';
}

$pakaianInfo = null;
if ($selected_pakaian) {
    $stmt = $conn->prepare("
    SELECT p.*, 
           pr.status AS status_pertukaran, pr.idPertukaran, pr.idPakaianTukar, pr.noMatrikPeminta, pr.noMatrikPemilik,
           perc.idPercuma,
           CASE 
               WHEN pr.idPertukaran IS NOT NULL THEN 'pertukaran'
               WHEN perc.idPercuma IS NOT NULL THEN 'percuma'
               ELSE NULL 
           END AS jenis_pemberian
    FROM tbl_pakaian p
    LEFT JOIN tbl_pertukaran pr ON pr.idPakaianTarget = p.idPakaian AND pr.status = 'diterima'
    LEFT JOIN tbl_percuma perc ON perc.idPakaian = p.idPakaian AND perc.status = 'Diterima'
    WHERE p.idPakaian = ?
    LIMIT 1
");

    $stmt->execute([$selected_pakaian]);
    $pakaianInfo = $stmt->fetch();

    // Override jenis pemberian jika ada rekod pertukaran diterima
    if (!empty($pakaianInfo['jenis_pemberian_override'])) {
        $pakaianInfo['jenis_pemberian'] = 'pertukaran';
    }
}


// Auto-clear mesej belum dibaca bila buka chat
if ($selected_receiver) {
    $stmt = $conn->prepare("UPDATE tbl_komunikasi 
        SET sudah_dibaca = 1 
        WHERE penerima = ? AND penghantar = ?");
    $stmt->execute([$no_matrik, $selected_receiver]);
}

// 1. Dapatkan semua chat berbeza pasangan
$stmt = $conn->prepare("SELECT k.*, 
    p.namaPakaian AS namaPakaianPemilik, p.gambar, p.jenis_pemberian,
    u.nama AS namaLawan,u.gambar_profil AS gambarLawan,
    IF(k.penghantar = ?, k.penerima, k.penghantar) AS lawan_matrik
FROM tbl_komunikasi k
JOIN tbl_pakaian p ON k.idPakaian = p.idPakaian
JOIN tbl_pengguna u ON u.noMatrik = IF(k.penghantar = ?, k.penerima, k.penghantar)
WHERE k.penghantar = ? OR k.penerima = ?
ORDER BY k.tarikhMesej DESC
");

$stmt->execute([$no_matrik, $no_matrik, $no_matrik, $no_matrik]);
$all_messages = $stmt->fetchAll();

// Group chat untuk paparan kiri
$chatList = [];
foreach ($all_messages as $msg) {
    $key = $msg['lawan_matrik'];
    if (!isset($chatList[$key])) {
        $chatList[$key] = [
            'noMatrikLawan' => $key,
            'namaLawan' => $msg['namaLawan'],
            'idPakaian' => $msg['idPakaian'],
            'mesej_akhir' => $msg['kandunganMesej'],
            'gambarLawan' => (!empty($msg['gambarLawan']) && file_exists($msg['gambarLawan'])) ? $msg['gambarLawan'] : 'profile-user.png'
        ];
    }
}


// Ambil mesej penuh jika chat dipilih
$chatMessages = [];
if ($selected_receiver) {
    $stmt = $conn->prepare("SELECT k.*, 
        p.namaPakaian, p.gambar, p.jenis_pemberian,
        pr.idPakaianTukar, pt.namaPakaian AS pakaian_tukar,
        pr.noMatrikPeminta, pr.noMatrikPemilik,
        u1.nama AS nama_peminta, u2.nama AS nama_pemilik
        FROM tbl_komunikasi k
        JOIN tbl_pakaian p ON k.idPakaian = p.idPakaian
        LEFT JOIN tbl_pertukaran pr ON pr.idPakaianTarget = p.idPakaian AND pr.status = 'diterima'
        LEFT JOIN tbl_pakaian pt ON pr.idPakaianTukar = pt.idPakaian
        LEFT JOIN tbl_pengguna u1 ON u1.noMatrik = pr.noMatrikPeminta
        LEFT JOIN tbl_pengguna u2 ON u2.noMatrik = pr.noMatrikPemilik
        WHERE (k.penghantar = ? AND k.penerima = ?) OR (k.penghantar = ? AND k.penerima = ?)
        ORDER BY k.tarikhMesej ASC");
    $stmt->execute([$no_matrik, $selected_receiver, $selected_receiver, $no_matrik]);
    $chatMessages = $stmt->fetchAll();
}
function paparHeaderUrusan($pakaianInfo, $conn) {
    $jenis = !empty($pakaianInfo['idPakaianTukar']) ? 'pertukaran' : ($pakaianInfo['jenis_pemberian'] ?? 'percuma');

    if ($jenis === 'pertukaran') {
        // Ambil maklumat pertukaran
        $stmt = $conn->prepare("SELECT pr.*, 
            pt.namaPakaian AS namaPakaianPeminta, 
            p.namaPakaian AS namaPakaianPemilik,
            u1.nama AS namaPeminta, 
            u2.nama AS namaPemilik
            FROM tbl_pertukaran pr
            LEFT JOIN tbl_pakaian pt ON pr.idPakaianTukar = pt.idPakaian
            LEFT JOIN tbl_pakaian p ON pr.idPakaianTarget = p.idPakaian
            LEFT JOIN tbl_pengguna u1 ON u1.noMatrik = pr.noMatrikPeminta
            LEFT JOIN tbl_pengguna u2 ON u2.noMatrik = pr.noMatrikPemilik
            WHERE pr.idPakaianTarget = ? AND pr.status = 'diterima'
            LIMIT 1");
        $stmt->execute([$pakaianInfo['idPakaian']]);
        $pertukaran = $stmt->fetch();
    
        $pakaianPeminta = htmlspecialchars($pertukaran['namaPakaianPeminta'] ?? '-');
        $pakaianPemilik = htmlspecialchars($pertukaran['namaPakaianPemilik'] ?? '-');
        $namaPeminta = htmlspecialchars($pertukaran['namaPeminta'] ?? 'Peminta');
        $namaPemilik = htmlspecialchars($pertukaran['namaPemilik'] ?? 'Pemilik');
    
        echo "<strong>Anda sedang berurusan tentang pertukaran ini:</strong><br>";
        echo "<div style='font-size: 0.85rem; margin-top: 5px;'>
                Pakaian Pemohon <strong>$namaPeminta</strong>: $pakaianPeminta<br>
                Pakaian Pemilik <strong>$namaPemilik</strong>: $pakaianPemilik
              </div>";
        echo '<a href="detail_pertukaran.php?id=' . $pakaianInfo['idPertukaran'] . '" style="color: #3c7962; font-weight: 500; font-size: 0.9rem; text-decoration: none;" 
            onmouseover="this.style.textDecoration=\'underline\'" 
            onmouseout="this.style.textDecoration=\'none\'">
            Butiran Pakaian
        </a>';
        return;
    }
    
    if ($jenis === 'percuma') {
        $gambar = explode(',', $pakaianInfo['gambar'])[0];
    
        echo '<div class="d-flex align-items-center gap-3">'; // No 'alert' or 'border'
        echo '<img src="' . $gambar . '" width="60" height="60" style="object-fit: cover; border-radius: 10px;">';
        echo '<div>';
        echo '<strong>Anda sedang berurusan tentang pemberian ini:</strong><br>';
        echo '<span style="font-weight: 600; color: #3c7962;">' . htmlspecialchars($pakaianInfo['namaPakaian']) . '</span><br>';
        echo '<a href="detail_percuma.php?id=' . $pakaianInfo['idPercuma'] . '" style="color: #3c7962; font-weight: 500; font-size: 0.9rem; text-decoration: none;" 
                  onmouseover="this.style.textDecoration=\'underline\'" 
                  onmouseout="this.style.textDecoration=\'none\'">
                  Butiran Pakaian
              </a>';
        echo '</div>';
        echo '</div>';
        return;
    }
    
}   
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReWear: Mesej </title>
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
    background-color: #f2f2f2;
}
.left-pane {
    width: 25%;
    height: 100vh;
    background: #fff;
    border-right: 1px solid #ccc;
    overflow-y: auto;
}
.right-pane {
    width: 75%;
    height: 100vh;
    background: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.chat-list-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}
.chat-list-item:hover {
    background-color: #f9f9f9;
}
.chat-box {
    flex-grow: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}
.bubble {
    padding: 10px 15px;
    margin-bottom: 10px;
    border-radius: 20px;
    max-width: 60%;
}
.bubble-left {
    background-color: #eee;
    align-self: flex-start;
}
.bubble-right {
    background-color: #c0d5c3;
    align-self: flex-end;
}
.home-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 1.8rem;
    color: #3c7962;
    text-decoration: none;
    z-index: 999;
    transition: color 0.3s;
}

.home-icon:hover {
    color: #2C4230;
}
h4.text-center.p-3 {
    font-weight: 700;
    font-size: 1.8rem;
    color: #3c7962; 
    border-bottom: 2px solid #3c7962;
   
    margin-bottom: 15px;
    letter-spacing: 1px;
    position: relative;
}

h4.text-center.p-3::before {
    content: "\f4ad"; /* ikon FontAwesome chat */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 8px;
}
.right-pane .text-muted {
    font-size: 1.2rem;
    color: #6c757d;
}
/* For Mobile View */
        @media (max-width: 768px) {
            .left-pane {
                position: fixed;
                top: 0;
                left: -250px; /* Initially hidden off-screen */
                width: 250px;
                height: 100%;
                background-color: #fff;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
                transition: left 0.3s ease-in-out;
                z-index: 9999;
            }
            .left-pane.open {
                left: 0; /* Show sidebar when it has the 'open' class */
            }

            .toggle-sidebar-btn {
                position: fixed;
                top: 20px;
                left: 20px;
                font-size: 2rem;
                color: #3c7962;
                z-index: 10000;
                cursor: pointer;
                background: none; /* Transparent background */
                border: none; /* Remove border */
                outline: none; /* Remove outline on focus */
                padding: 0; /* Remove padding */
                transition: transform 0.2s ease;
            }
            .toggle-sidebar-btn:hover {
                transform: scale(1.2); /* Slight zoom on hover */
            }
             /* Right Pane (Chat area) */
            .right-pane {
                width: 100%; /* Fill the screen width */
                height: 100vh;
                background: #fff;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding-right: 0; /* No extra padding on the right */
                margin-right: 0; /* No margin on the right */
                box-sizing: border-box; /* Include padding in the width calculation */
            }

            .home-icon {
                position: fixed;
                top: 20px;
                right: 20px; /* Move it to the right */
                font-size: 1.8rem;
                color: #3c7962;
                z-index: 10000;
                cursor: pointer;
                display: block;  /* Ensure it’s visible */
            }
            chat-box {
            flex-grow: 1; /* This ensures the chat box fills up available space */
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            }
            
        }

        /* For Desktop View */
        @media (min-width: 769px) {
        .toggle-sidebar-btn {
        display: none; /* Hide hamburger button on desktop */
        }

    .left-pane {
        position: static;
        width: 25%;
        height: 100vh;
        background-color: #fff;
        border-right: 1px solid #ccc;
        overflow-y: auto;
    }
}

</style>
</head>
<body>
<div class="d-flex">
    <!-- Button to open sidebar on mobile -->
    <button class="toggle-sidebar-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i> <!-- Hamburger icon -->
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="left-pane">
        <!-- Home Icon -->
        <a href="index.php" class="home-icon" title="Halaman Utama">
            <i class="fas fa-home"></i>
        </a>

        <h4 class="text-center p-3">MESEJ</h4>

        <!-- Chat List -->
        <?php foreach ($chatList as $chat): ?>
            <a href="?chat_with=<?= $chat['noMatrikLawan'] ?>&idPakaian=<?= $chat['idPakaian'] ?>" class="text-decoration-none text-dark">
                <div class="chat-list-item d-flex align-items-center">
                    <img src="<?= $chat['gambarLawan'] ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                    <div>
                        <strong><?= $chat['namaLawan'] ?></strong><br>
                        <small><?= htmlspecialchars($chat['mesej_akhir']) ?></small>
                        
                        <!-- Unread messages count -->
                        <?php 
                        $stmt_unread = $conn->prepare("SELECT COUNT(*) FROM tbl_komunikasi WHERE penerima = ? AND penghantar = ? AND sudah_dibaca = 0");
                        $stmt_unread->execute([$no_matrik, $chat['noMatrikLawan']]);
                        $unreadCount = $stmt_unread->fetchColumn();
                        if ($unreadCount > 0): 
                        ?>
                            <span class="badge bg-success ms-2"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>


    <div class="right-pane">
    <?php if ($receiverInfo): ?>
    <div class="d-flex align-items-center justify-content-center p-3 border-bottom" style="gap: 10px;">
        <img src="<?= $gambarReceiver ?>" alt="Gambar" width="50" height="50" style="object-fit: cover; border-radius: 50%;">
        <div>
        <strong><?= htmlspecialchars($receiverInfo['nama'] ?? 'Pengguna') ?></strong><br>
        </div>
    </div>
    <?php endif; ?>

    <?php
if ($receiverInfo && !empty($pakaianInfo)) {
    if (!empty($pakaianInfo['idPakaianTukar'])) {
        // Jika ada idPakaianTukar, itu pertukaran
        echo '<div class="alert alert-light border rounded d-flex justify-content-between align-items-center mx-3 my-2" style="padding: 15px;">';
        echo '<div class="d-flex align-items-center gap-3"><div>';
        paparHeaderUrusan($pakaianInfo, $conn);
        echo '</div></div></div>';
    } else {
        // Kalau tiada, itu percuma
        echo '<div class="alert alert-light border rounded d-flex justify-content-between align-items-center mx-3 my-2" style="padding: 15px;">';
        echo '<div class="d-flex align-items-center gap-3"><div>';
        paparHeaderUrusan($pakaianInfo, $conn);
        echo '</div></div></div>';
    }
}



?>
<!-- Chat container bermula di bawah -->
<div class="chat-box" id="chatContainer">

<?php 
$lastPakaianId = null;
foreach ($chatMessages as $msg): 
    // Skip mesej yang pakaian sama seperti yang dipaparkan di header atas
    if (!empty($pakaianInfo['idPakaian']) 
    && $msg['idPakaian'] == $pakaianInfo['idPakaian']
    && (
        ($pakaianInfo['jenis_pemberian'] === 'pertukaran' && !empty($msg['idPakaianTukar'])) ||
        ($pakaianInfo['jenis_pemberian'] === 'percuma' && empty($msg['idPakaianTukar']))
    )
) {
    continue;
}


    $own = $msg['penghantar'] == $no_matrik;
    $class = $own ? 'bubble-right' : 'bubble-left';

    $jenisDalamLoop = !empty($msg['idPakaianTukar']) ? 'pertukaran' : 'percuma';


    // Papar header hanya sekali setiap pakaian berbeza
    if ($lastPakaianId !== $msg['idPakaian']) {
        $lastPakaianId = $msg['idPakaian'];
        $butiranLink = ($jenisDalamLoop === 'pertukaran') 
            ? 'detail_pertukaran.php?id=' . $msg['idPakaian']
            : 'detail_percuma.php?id=' . $msg['idPakaian'];

        echo '<div class="alert alert-light border rounded d-flex align-items-center gap-3 mb-2" style="padding: 10px;">';
        echo '<a href="' . $butiranLink . '" style="color: #3c7962; font-weight: 600; text-decoration: none;" 
                 onmouseover="this.style.textDecoration=\'underline\'" 
                 onmouseout="this.style.textDecoration=\'none\'">'
             . htmlspecialchars($msg['namaPakaian']) .
             '</a>';
        echo '</div>';
    }

    // Papar mesej
    ?>
    <div class="bubble <?= $class ?>">
        <?= htmlspecialchars($msg['kandunganMesej']) ?>
    </div>
<?php endforeach; ?>
        </div>
        <?php if ($selected_receiver): ?>
            <form id="messageForm" class="d-flex p-3">
            <input type="hidden" name="pakaian_id" value="<?= $selected_pakaian ?>">
            <input type="hidden" name="receiver_id" value="<?= $selected_receiver ?>">
            <input type="text" name="content" id="messageInput" class="form-control me-2" placeholder="Tulis mesej..." required>
            <button type="submit" class="btn btn-success">Hantar</button>
        </form>

        <script>

function isUserAtBottom() {
    const chatBox = document.getElementById('chatContainer');
    return chatBox.scrollHeight - chatBox.clientHeight - chatBox.scrollTop <= 50;
}

function loadMessages() {
    const chatBox = document.getElementById('chatContainer');
    const atBottom = isUserAtBottom(); // semak user di bawah ke tak

    fetch('fetch_messages.php?chat_with=<?= $selected_receiver ?>&idPakaian=<?= $selected_pakaian ?>')
        .then(response => response.text())
        .then(data => {
            chatBox.innerHTML = data;
            if (atBottom) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
}
document.getElementById('chatContainer').addEventListener('scroll', function() {
    // optional jika nak buat indicator nanti
});


document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('send_message.php', {
        method: 'POST',
        body: formData
    }).then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('messageInput').value = '';
            loadMessages(); // auto refresh message list
        } else {
            alert(data.message);
        }
    });
});
// auto-load mesej setiap 3 saat
setInterval(loadMessages, 3000);

// load mesej pertama kali bila buka
loadMessages();

// scroll ke bawah bila pertama kali buka
setTimeout(() => {
    const chatBox = document.getElementById('chatContainer');
    chatBox.scrollTop = chatBox.scrollHeight;
}, 500); // beri masa untuk fetch siap

</script>


        <?php else: ?>
        <!-- Jika belum pilih sesiapa -->
         <div class="d-flex flex-column align-items-center justify-content-center h-100">
        <img src="logorewear.png" alt="Pilih chat" style="width: 380px; height: auto; margin-bottom: 20px;">
        <h5 class="text-muted">Pilih Mesej untuk Teruskan</h5>
    </div>
<?php endif; ?>
    </div>
</div>
<script>
   
     function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("open");
    }
</script>

</body>
</html>

