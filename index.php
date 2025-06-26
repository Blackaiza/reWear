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
?>
<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ReWear: Utama</title>

   <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
/* General Body Styles */
body {
  font-family: 'Poppins', sans-serif;
  background-color: #fff;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Hero Section */
.hero-section {
    position: relative;
    background-image: url('bg.jpg'); /* Static background image */
    background-size: cover; /* Ensures the background image covers the full section */
    background-position: center center; /* Centers the image */
    background-repeat: no-repeat;
    color: white;
    padding: 5vh 2rem;
    height: 92vh; /* Full height for the hero section */
    display: grid;
    place-items: center;
    overflow: hidden;
}

.hero-section .container {
    position: relative;
    z-index: 2;
    text-align: center;
    margin-top: -35vh;
}

.hero-section h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.hero-section p {
    font-size: 1.25rem;
    margin-bottom: 30px;
    font-weight: 400;
}

.cta-btn {
    background: #ffffff;
    color: #000;
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.cta-btn:hover {
    background: #f1f1f1;
    transform: scale(1.05);
}

/* Mobile View Adjustments */
@media (max-width: 768px) {
    .hero-section {
        padding: 20vh 2rem;
    }

    .hero-section h1 {
        font-size: 2.5rem;
    }

    .hero-section p {
        font-size: 1.1rem;
    }
}

/* Footer */
footer {
    background-color: #3c7962;
    color: #fff;
    text-align: center;
    padding: 15px 0;
    font-weight: 500;
}

/* General Footer Styles */
footer a {
    color: #ccc;
    text-decoration: none;
}

footer a:hover {
    color: white;
}
</style>
</head>
<body>

<!-- Navbar -->
<?php include 'nav_bar.php'; ?>

<section class="hero-section">
    <!-- Background images transition -->
    <div class="transition-images"></div> <!-- This div controls the background image transition -->
    <div class="container">
        <h1 class="display-4 fw-bold">Selamat Datang ke ReWear</h1>
        <p class="lead">Anda telah log masuk sebagai pentadbir.</p>
        
    </div>
</section>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init(); // Inisialisasi AOS
</script>


</body>
</html>
