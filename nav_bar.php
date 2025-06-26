<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #fff; padding: 0.5rem 1.5rem; border-bottom: 1px solid #ddd;">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand" href="indexadmin.php" style="color: #3c7962; font-weight: 700; font-size: 1.5rem;">ReWear</a>

    <!-- Mobile Menu Toggle -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAdmin" aria-controls="navbarNavAdmin" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Items -->
    <div class="collapse navbar-collapse" id="navbarNavAdmin">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link" href="index.php" style="color: #3c7962; font-weight: 600; margin: 0 15px; font-size: 0.95rem;">UTAMA</a></li>
        <li class="nav-item"><a class="nav-link" href="analisis_graf.php" style="color: #3c7962; font-weight: 600; margin: 0 15px; font-size: 0.95rem;">ANALISIS DATA</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="upcycleDropdown" role="button" data-toggle="dropdown" aria-expanded="false" style="color: #3c7962; font-weight: 600; margin: 0 15px; font-size: 0.95rem;">UPCYLCE</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="tipsdiy.php">Tips DIY</a></li>
            <li><a class="dropdown-item" href="tukangjahit.php">Senarai Tukang Jahit</a></li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- User Profile -->
    <div class="d-flex align-items-center">
      <span class="me-2" style="color: #3c7962; font-weight: 600;">Selamat Datang, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Pentadbir'; ?>!</span>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
            <?php
            if (isset($_SESSION['no_matrik'])) {
                include 'database.php';
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT gambar_profil FROM tbl_pengguna WHERE noMatrik = ?");
                $stmt->execute([$_SESSION['no_matrik']]);
                $data_navbar = $stmt->fetch();

                $gambar_profil = (!empty($data_navbar['gambar_profil']) && file_exists($data_navbar['gambar_profil']))
                    ? $data_navbar['gambar_profil']
                    : 'profile-user.png';
            } else {
                $gambar_profil = 'profile-user.png';
            }
            ?>
            <img src="<?= $gambar_profil ?>" alt="User" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; cursor: pointer; transition: border-color 0.3s ease;">
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="logout.php">Log Keluar</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Styling -->
<style>
/* General Navbar */
.navbar {
  background-color: #fff; /* White background */
  padding: 0.5rem 1.5rem;
  border-bottom: 1px solid #ddd;
}

/* Logo */
.navbar-brand {
  color: #3c7962;
  font-weight: 700;
  font-size: 1.5rem;
}

.navbar-brand:hover {
  color: #2c6c5f;
}

/* Nav Links */
.nav-link {
  color: #3c7962;
  font-weight: 600;
  margin: 0 15px;
  font-size: 0.95rem;
  text-transform: uppercase;
}

.nav-link:hover {
  color: #2c6c5f;
}

/* User Profile */
.user-greeting {
  color: #3c7962;
  font-weight: 600;
  margin-right: 10px;
}

/* Profile Image */
.profile-pic {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  cursor: pointer;
  transition: border-color 0.3s ease;
}

.profile-pic:hover {
  border-color: #6aae7d;
}

/* Mobile-friendly Styling */
@media (max-width: 768px) {
  /* Hide "Selamat Datang" on larger screens and move it to the hamburger */
  .user-greeting {
    display: none; /* Hide "Selamat Datang" on mobile */
  }

  .navbar-nav {
    display: block;
  }

  .navbar-nav .nav-item {
    margin: 5px 0;
  }

  .navbar-nav .nav-link {
    padding-left: 10px;
    padding-right: 10px;
    font-size: 1rem;
  }

  .navbar-toggler {
    margin-left: auto;
  }
}
</style>
