<?php
session_start();
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
  <meta charset="UTF-8">
  <title>ReWear: Analisis Data</title>

<!-- Fonts & Library -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <!-- Google Fonts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS after CSS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js after Bootstrap JS -->


  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color:rgb(255, 255, 255);
      margin: 0;
      padding: 0;
    }

    .wrapper {
      max-width: 1000px;
      margin: 60px auto;
      background: white;
      border-radius: 20px;
      padding: 40px 50px;
      box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.07);
    }

    h2 {
      text-align: center;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .form-section {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    select, button {
      padding: 10px 18px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-family: 'Poppins', sans-serif;
    }

    button {
      background-color: #3c7962;
      color: white;
      border: none;
      transition: 0.3s ease;
    }

    button:hover {
      background-color: #2f5e4c;
    }

    .chart-container {
      position: relative;
      height: 360px;
      width: 100%;
    }

    @media screen and (max-width: 768px) {
      .form-section {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>

<?php include 'nav_bar.php'; ?>

<div class="wrapper">
  <h2>Analisis Data Pakaian</h2>

  <div class="form-section">
    <label for="kategori" class="fw-semibold">Kategori Pakaian</label>
    <select id="kategori">
      <option value="Lelaki">Pakaian Lelaki</option>
      <option value="Wanita">Pakaian Perempuan</option>
    </select>
    <button onclick="loadChart()">Hasilkan Graf</button>
  </div>

  <div class="chart-container mb-5">
  <canvas id="grafDiberi"></canvas>
</div>

<div class="chart-container">
  <canvas id="grafKatalog"></canvas>
</div>

</div>

<script>
let chartDiberi, chartKatalog;

function loadChart() {
  const kategori = document.getElementById('kategori').value;
  fetch("analisis_data.php?kategori=" + kategori)
    .then(response => response.json())
    .then(data => {
      const ctxDiberi = document.getElementById('grafDiberi').getContext('2d');
      const ctxKatalog = document.getElementById('grafKatalog').getContext('2d');

      // Tukar nama bulan penuh kepada ringkasan (optional)
      data.labels = data.labels.map(label => {
        const shortMonth = {
          'Januari': 'Jan', 'Februari': 'Feb', 'Mac': 'Mac', 'April': 'Apr',
          'Mei': 'Mei', 'Jun': 'Jun', 'Julai': 'Jul', 'Ogos': 'Ogos',
          'September': 'Sep', 'Oktober': 'Okt', 'November': 'Nov', 'Disember': 'Dis'
        };
        return shortMonth[label] || label;
      });

      if (chartDiberi) chartDiberi.destroy();
      if (chartKatalog) chartKatalog.destroy();

      chartDiberi = new Chart(ctxDiberi, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Jumlah Pakaian yang telah diberi',
            data: data.diberi,
            backgroundColor: '#3c7962',
            borderRadius: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'Pakaian Telah Diberi - ' + kategori,
              font: {
                family: 'Poppins',
                size: 18,
                weight: 'bold'
              },
              color: '#222',
              padding: { top: 10, bottom: 20 }
            },
            legend: {
              labels: {
                font: {
                  family: 'Poppins',
                  size: 14,
                  weight: '500'
                },
                color: '#444'
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  family: 'Poppins',
                  size: 13
                },
                color: '#333'
              }
            },
            x: {
              ticks: {
                font: {
                  family: 'Poppins',
                  size: 13
                },
                color: '#333',
                maxRotation: 0,
                minRotation: 0
              }
            }
          }
        }
      });

      chartKatalog = new Chart(ctxKatalog, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Jumlah Pakaian yang telah dimuat naik',
            data: data.katalog,
            backgroundColor: '#7fb77e',
            borderRadius: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: true,
              text: 'Pakaian Dimuat Naik - ' + kategori,
              font: {
                family: 'Poppins',
                size: 18,
                weight: 'bold'
              },
              color: '#222',
              padding: { top: 10, bottom: 20 }
            },
            legend: {
              labels: {
                font: {
                  family: 'Poppins',
                  size: 14,
                  weight: '500'
                },
                color: '#444'
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  family: 'Poppins',
                  size: 13
                },
                color: '#333'
              }
            },
            x: {
              ticks: {
                font: {
                  family: 'Poppins',
                  size: 13
                },
                color: '#333',
                maxRotation: 0,
                minRotation: 0
              }
            }
          }
        }
      });
    });
}
window.onload = loadChart;
</script>

</body>
</html>
