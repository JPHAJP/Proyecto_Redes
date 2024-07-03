<?php
// API Key para la cocina
$API_KEY = '123';

// Inicializar variables
$hum = 0;
$mov = 0;
$dist = 0;
$status = "0";
$time = "00:00:00";
$color_verde = 'gray';
$color_amarillo = 'gray';
$color_rojo = 'gray';

// Comprobar si el archivo log.txt existe, si no, crearlo con contenido inicial
if (!file_exists("log.txt")) {
    file_put_contents("log.txt", "Hum: 0, Mov: 0, Dist: 0, Status: 0, Time: 00:00:00\n");
}

// Leer el archivo para obtener los datos actuales
$fileContent = file_get_contents("log.txt");

if ($fileContent !== false) {
    // Obtener la última línea del archivo
    $lines = explode("\n", trim($fileContent));
    $lastLine = end($lines);

    // Extraer los valores de la última línea
    preg_match('/Hum: (\d+), Mov: (\d+), Dist: (\d+), Status: (\d+), Time: (.+)/', $lastLine, $matches);
    if ($matches) {
        $hum = $matches[1];
        $mov = $matches[2];
        $dist = $matches[3];
        $status = $matches[4];
        $time = $matches[5];
    }
}

// Procesar los datos de GET solo si la API Key es correcta
if (isset($_GET['apikey']) && $_GET['apikey'] === $API_KEY) {
    $hum = isset($_GET['hum']) ? htmlspecialchars($_GET['hum'], ENT_QUOTES, 'UTF-8') : $hum;
    $mov = isset($_GET['mov']) ? htmlspecialchars($_GET['mov'], ENT_QUOTES, 'UTF-8') : $mov;
    $dist = isset($_GET['dist']) ? htmlspecialchars($_GET['dist'], ENT_QUOTES, 'UTF-8') : $dist;
    $status = isset($_GET['status']) ? htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8') : $status;
    $time = isset($_GET['time']) ? htmlspecialchars($_GET['time'], ENT_QUOTES, 'UTF-8') : $time;
    
    // Crear una cadena de texto con los datos recibidos
    $dataString = "Hum: " . $hum . ", Mov: " . $mov . ", Dist: " . $dist . ", Status: " . $status . ", Time: " . $time . "\n";
    
    // Especificar el archivo donde se guardarán los datos
    $filePath = "log.txt";
    
    // Abrir el archivo en modo append
    $file = fopen($filePath, "a");
    
    // Verificar si el archivo se ha abierto correctamente
    if ($file !== false) {
        // Escribir los datos en el archivo y cerrarlo
        fwrite($file, $dataString);
        fclose($file);
        $message = "Datos actualizados.";
    } else {
        $message = "Error opening the file " . $filePath;
    }
} else {
    $message = "Mostrando datos actuales.";
}

// Determinar el color del semáforo según el valor de $hum
if ($dist > 7) {
    $color_verde = 'green';
    $color_amarillo = 'gray';
    $color_rojo = 'gray';
} elseif ($dist > 2) {
    $color_verde = 'gray';
    $color_amarillo = 'yellow';
    $color_rojo = 'gray';
} else {
    $color_verde = 'gray';
    $color_amarillo = 'gray';
    $color_rojo = 'red';
}

// Función para traducir el estado
function translateStatus($status) {
    switch ($status) {
        case '0':
            return 'activo';
        case '1':
            return 'detenido';
        case '2':
            return 'terminado';
        default:
            return 'desconocido';
    }
}

$translatedStatus = translateStatus($status);

echo $message;
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="refresh" content="10">
<head>
  <style>
    body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .container-semaphore {
      text-align: center;
    }
    .semaphore {
      width: 180px;
      height: 100px;
      background-color: #333;
      border-radius: 30px;
      display: flex;
      align-items: center;
      justify-content: space-around;
      margin: 20px auto;
      padding: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    .light {
      width: 30px;
      height: 30px;
      border-radius: 50%;
    }
    .light.green {
      background-color: <?php echo $color_verde; ?>;
    }
    .light.yellow {
      background-color: <?php echo $color_amarillo; ?>;
    }
    .light.red {
      background-color: <?php echo $color_rojo; ?>;
    }
    .text-semaphore {
      text-align: center;
      color: #ec6090;
      font-size: 20px;
      font-weight: bold;
      margin-top: 20px;
    }
    .sensor-container {
      background: #333;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 200px;
    }
    .thermometer {
      width: 35px;
      height: 150px;
      border: 2px solid #fff;
      border-radius: 25px;
      position: relative;
      background: #333;
      margin: 0 auto;
    }
    .thermometer-fill {
      width: 100%;
      position: absolute;
      bottom: 0;
      border-radius: 0 0 25px 25px;
    }
    .thermometer-scale {
      position: absolute;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .thermometer-scale div {
      text-align: center;
      margin-right: 5px;
      font-size: 0.5em;
      color: #fff;
    }
    .humidity-value {
      text-align: center;
      color: #ec6090;
      font-size: 20px;
      font-weight: bold;
      margin-top: 20px;
    }
    .time {
        font-size: 38px;
        margin-bottom: 20px;
        color: #fff;
    }
    .status {
        font-size: 15px;
        color: #fff;
        padding: 10px;
        border-radius: 10px;
    }
    .status-activo { background-color: green; }
    .status-detenido { background-color: orange; }
    .status-terminado { background-color: red; }
    .contenedor {
      width: 190px; 
      height: 100%; 
      background-color: #333; 
      border-radius: 10px; 
      transition: background-color 0.5s;
    }
    .techo {
      position: relative;
      left: 50%; 
      transform: translateX(-50%); 
      width: 90%; 
      height: 30px; 
      background-color: #1f2122; 
      border-radius: 10px;
    }
    .foco {
      width: 25px;
      height: 25px;
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      background-color: #fff;
      border-radius: 0 0 50% 50%;
      box-shadow: 0 0 10px 5px yellow;
      transition: opacity 0.5s;
    }
    .cama {
      position: absolute;
      top: 100px;
      left: 50%; 
      transform: translateX(-50%);
      width: 150px; 
      height: 110px;
      background-color: #ec6090; 
      border-radius: 10px 10px 0 0;
      box-shadow: 0 0 10px 5px rgba(0,0,0,0.5); 
    }
    .almohada {
      width: 70px;
      height: 30px;
      background-color: #fff;
      border-radius: 10px 10px 0 0;
      box-shadow: 0 0 10px 5px rgba(0,0,0,0.5);
      position: absolute;
      top: 10px;
    }
    .almohada.izquierda {
      left: 3px;
    }
    .almohada.derecha {
      right: 3px;
    }
  </style>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <title>Smart Home</title>
  <link href=../vendor/bootstrap/css/bootstrap.min.css rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/fontawesome.css">
  <link rel="stylesheet" href="../assets/css/templatemo-cyborg-gaming.css">
  <link rel="stylesheet" href="../assets/css/owl.css">
  <link rel="stylesheet" href="../assets/css/animate.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
</head>
<body>
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <header class="header-area header-sticky">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <a href="/dashboard" class="logo">
              <img src="../assets/images/Smart_Home_logo.png" alt="">
            </a>
            <ul class="nav">
              <li><a href="../">Cerrar Sesion</a></li>
            </ul>
            <a class='menu-trigger'>
              <span>Menu</span>
            </a>
          </nav>
        </div>
      </div>
    </div>
  </header>
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-content">
          <div class="main-banner">
            <div class="row">
              <div class="col-lg-7">
                <div class="header-text">
                  <h6>Sistema de domotica para el hogar</h6>
                  <br>
                  <h4><em>Dashboard</em> Sensores</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="most-popular">
            <div class="row">
              <div class="col-lg-12">
                <div class="heading-section">
                  <h4>Menu de Sensores</h4>
                </div>
                <div class="row">
                  <div class="col-lg-6 col-sm-6">
                    <div class="item">
                      <div class="container">
                          <div class="time"><?php echo $time; ?></div>
                          <div class="status status-<?php echo $translatedStatus; ?>">Status: <?php echo $translatedStatus; ?></div>
                      </div>
                      <h4>Cocina</h4>                  
                    </div>
                  </div>
                  <div class="col-lg-6 col-sm-6">
                    <div class="item">
                      <div class="container-semaphore">
                        <div class="semaphore">
                          <div class="light red"></div>
                          <div class="light yellow"></div>
                          <div class="light green"></div>
                        </div>
                        <div class="text-semaphore">Distancia: <?php echo $dist; ?> cm</div>
                      </div>
                      <h4>Cochera</h4>                  
                    </div>
                  </div>
                  <div class="col-lg-6 col-sm-6">
                    <div class="item">
                      <div class="thermometer">
                        <div class="thermometer-fill" id="thermometer-fill" style="height: 0;"></div>
                        <div class="thermometer-scale">
                          <div>100%</div>
                          <div>80%</div>
                          <div>60%</div>
                          <div>40%</div>
                          <div>20%</div>
                          <div>0%</div>
                        </div>
                      </div>
                      <div class="humidity-value" id="humidity-value">
                        <?php
                          if (isset($hum)) {
                            echo "Humedad: $hum%";
                          } else {
                            echo "Humedad: N/A";
                          }
                        ?>
                      </div>
                      <script>
                          var humedad = <?php echo isset($hum) ? $hum : 0; ?>;
                          var thermometerFill = document.getElementById('thermometer-fill');
                          thermometerFill.style.height = humedad + '%';
                          if (humedad >= 80) {
                            thermometerFill.style.background = '#0B3D91'; 
                          } else if (humedad >= 60) {
                            thermometerFill.style.background = '#4682B4'; 
                          } else if (humedad >= 40) {
                            thermometerFill.style.background = '#87CEEB'; 
                          } else if (humedad >= 20) {
                            thermometerFill.style.background = '#FFD700'; 
                          } else {
                            thermometerFill.style.background = '#FF4500'; 
                          }
                          var humidityValue = document.getElementById('humidity-value');
                          humidityValue.textContent = 'Humedad: ' + humedad + '%';
                      </script>
                      <h4>Baño</h4>
                    </div>
                  </div>
                  <div class="col-lg-6 col-sm-6">
                    <div class="item">
                      <div class="contenedor" style="background-color: <?php echo ($mov == 1) ? '#FFFF99' : '#555'; ?>;">
                        <div class="techo">
                          <h2 style="text-align: center; color: #ec6090;font-size: 15px;">
                            <?php
                            if ($mov == 1) {
                                echo "Cuarto encendido";
                            } else {
                                echo "Cuarto apagado";
                            }
                            ?>
                          </h2>
                        </div>
                        <div class="foco" style="opacity: <?php echo ($mov == 1) ? '1' : '0.1'; ?>;"></div>
                          <div class="cama" style="opacity: <?php echo ($mov != 1) ? '0.5' : '1'; ?>;">
                            <div class="almohada izquierda"></div>
                            <div class="almohada derecha"></div>
                          </div>
                        </div>
                        <h4>Cuarto</h4>
                  </div>
                </div>
                <p><?php echo $message; ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Proyecto Final Redes Digitales de Datos 2024
            <br>Máximo Arenas - Mary Tere Füguemann - Jose Pablo Hernández - Dirk Anton Topcic </p>
        </div>
      </div>
    </div>
  </footer>
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/js/isotope.min.js"></script>
  <script src="../assets/js/owl-carousel.js"></script>
  <script src="../assets/js/tabs.js"></script>
  <script src="../assets/js/popup.js"></script>
  <script src="../assets/js/custom.js"></script>
</body>
</html>
