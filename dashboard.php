<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ReWear - Berikan Pakaian Anda</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">

  <style>
  body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background:rgb(197, 226, 209);
    color: #2f4f4f;
  }

  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 5%;
    background-color: #ffffff;
  }

  .logo {
    font-size: 24px;
    font-weight: 600;
    color: #2c6c5f;
  }

  .logo span {
    color: #6aae80;
  }

  nav ul {
    list-style: none;
    display: flex;
    gap: 20px;
  }

  nav ul li a {
    text-decoration: none;
    color: #2f4f4f;
    font-weight: 500;
  }

  .buttons a {
    margin-left: 10px;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
  }

  .login {
  border: 1px solid #2c6c5f;
  color: #2c6c5f;
  background-color: transparent;
  transition: 0.3s;
}

.login:hover {
  background-color: #2c6c5f;
  color: white;
}

.register {
  background-color: #2c6c5f;
  color: white;
  transition: 0.3s;
}

.register:hover {
  background-color:rgb(21, 54, 46); /* warna hijau lebih gelap bila hover */
}


  /* HERO SECTION */
  .hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 40px 5%;
    height: 10h; /* tinggi 90% skrin */
    gap: 40px;
    
  }

  .hero-text {
    flex: 1;
  }

  .hero-text h1 {
    font-size: 36px;
    color: #2c6c5f;
    margin-bottom: 10px;
  }

  .hero-text p {
    margin-bottom: 20px;
  }

  .cta {
    display: inline-block;
    padding: 10px 20px;
    background: #2c6c5f;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* add shadow */
  transition: 0.3s;
}

.cta:hover {
  background: #276455; /* warna gelap sikit bila hover */
}

  .hero-image {
    flex: 1;
    display: flex;
    justify-content: center;
  }

  .hero-image img {
  width: 90%;
  max-width: 800px;
  height: auto;
  animation: float 3s ease-in-out infinite;
}

/* Animation Keyframes */
@keyframes float {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px); /* naik 10px */
  }
  100% {
    transform: translateY(0px);
  }
}


  .features {
    display: flex;
    justify-content: space-around;
    padding: 23px 5%;
    background-color: #e9f5ee;
  }

  .feature {
    text-align: center;
  }

  .feature img {
    width: 50px;
    margin-bottom: 10px;
  }

  /* RESPONSIVE */
  @media (max-width: 768px) {
    .hero {
      flex-direction: column;
      text-align: center;
      height: auto;
      padding: 40px 23px;
    }

    .hero-image img {
      width: 100%;
      max-width: 300px;
    }
    
  }
</style>

</head>

<body>
  <header>
    <div class="navbar">
      <div class="logo">Re<span>Wear</span></div>
      <div class="buttons">
        <a href="login.php" class="login">Log Masuk</a>
        <a href="daftar.php" class="register">Daftar</a>
      </div>
    </div>
  </header>

  <main class="hero">
    <div class="hero-text">
      <h1>Sama-Sama Berkongsi, <br> Sama-Sama Gembira!</h1>
      <p>Jom beri pakaian kepada yang memerlukan atau tukar baju dengan kawan-kawan di UKM!</p>
      <a href="login.php" class="cta">Mula Memberikan</a>
    </div>
    <div class="hero-image">
      <img src="illustration.svg" alt="Woman donating clothes">
    </div>
  </main>

  <section class="features">
    <div class="feature">
      <img src="icon1.png" alt="">
      <p>Pakaian Terpakai</p>
    </div>
    <div class="feature">
      <img src="icon2.png" alt="">
      <p>UKM,Bangi</p>
    </div>
    <div class="feature">
      <img src="icon3.png" alt="">
      <p>Petukaran Mudah</p>
    </div>
    <div class="feature">
      <img src="icon4.png" alt="">
      <p>Pemberian Pakaian</p>
    </div>
  </section>
</body>
</html>
