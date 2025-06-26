<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>ReWear Navbar</title>

<!-- Google Fonts: Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap 4.6.2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
}
.minimalist-navbar {
  background-color: #ffffff;
  padding: 0.8rem 1.2rem;
  border-bottom: 1px solid #eee;
}
.brand-re {
  color: #3c7962;
  font-weight: 700;
  font-size: 1.6rem;
}
.nav-link {
  color: #3c7962 !important;
  font-weight: 600;
  padding: 0.5rem 1rem;
}
.nav-link:hover {
  color: #2c6c5f !important;
}
.search-form {
  max-width: 220px;
  width: 100%;
}
.search-container {
  position: relative;
  width: 100%;
}
.search-input {
  width: 100%;
  border: 1px solid #3c7962;
  border-radius: 50px;
  padding: 0.3rem 0.8rem;
  padding-right: 35px;
  background-color: #f9f9f9;
  color: #333;
  font-family: 'Poppins', sans-serif;
  font-size: 0.85rem;
}
.search-button {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: none;
  color: #3c7962;
  font-size: 0.9rem;
  cursor: pointer;
}
.profile-pic {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  object-fit: cover;
}
.user-greeting {
  color: #3c7962;
  font-weight: 600;
  font-size: 0.9rem;
}
@media (max-width: 768px) {
  .search-form {
    order: 3;
    margin-top: 10px;
    max-width: 100%;
  }
  .user-greeting {
    font-size: 0.8rem;
  }
}
.dropdown-triangle {
  width: 0;
  height: 0;
  margin-left: 8px;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid #3c7962;
}

</style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light minimalist-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="indexnu.php"><span class="brand-re">ReWear</span>.</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mr-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="indexnu.php">Utama</a></li>
        <li class="nav-item"><a class="nav-link" href="katalog.php">Katalog</a></li>
        <li class="nav-item"><a class="nav-link" href="upcycle.php">Upcycle</a></li>
        <li class="nav-item"><a class="nav-link" href="paparan_mesej.php">Mesej</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Permintaan</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="pemberian_percuma.php">Pemberian Percuma</a>
            <a class="dropdown-item" href="pemberian_pertukaran.php">Pemberian Pertukaran</a>
          </div>
        </li>
      </ul>

      <form class="search-form mr-3" action="katalog.php" method="GET">
        <div class="search-container">
          <input type="search" name="search" class="search-input" placeholder="Cari...">
          <button type="submit" class="search-button">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>

      <div class="d-flex align-items-center">
        <span class="user-greeting mr-2">
          Selamat Datang, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Pengguna'; ?>!
        </span>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link p-0" href="#" data-toggle="dropdown">
              <?php
              if (isset($_SESSION['no_matrik'])) {
                  include 'database.php';
                  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  $stmt = $conn->prepare("SELECT gambar_profil FROM tbl_pengguna WHERE noMatrik = ?");
                  $stmt->execute([$_SESSION['no_matrik']]);
                  $data_navbar = $stmt->fetch();
                  $gambar_profil = (!empty($data_navbar['gambar_profil']) && file_exists($data_navbar['gambar_profil'])) ? $data_navbar['gambar_profil'] : 'profile-user.png';
              } else {
                  $gambar_profil = 'profile-user.png';
              }
              ?>
 <a class="nav-link p-0 d-flex align-items-center" href="#" data-toggle="dropdown">
    <img src="<?= $gambar_profil ?>" class="profile-pic">
    <span class="dropdown-triangle"></span>
</a>


            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="kemaskiniprofil.php">Profil</a>
              <a class="dropdown-item" href="muatnaik_form.php">Muat Naik Pakaian</a>
              <a class="dropdown-item" href="senaraipakaian.php">Senarai Pakaian</a>
              <a class="dropdown-item" href="faq.php">Soalan Lazim (FAQ)</a>
              <a class="dropdown-item" href="logout.php">Log Keluar</a>
            </div>
          </li>
        </ul>
      </div>

    </div>
  </div>
</nav>

<!-- Bootstrap 4.6.2 JS + jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
