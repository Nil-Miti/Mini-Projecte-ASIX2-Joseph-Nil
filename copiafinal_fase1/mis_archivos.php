<?php
session_start();

// Verificar si el usuario está autenticado y tiene permisos
if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    header("Location: /auth/login.php");
    exit();
}

// Conexión a la base de datos MySQL
$servername = "localhost";
$username = "admin";
$password = "1234";
$dbname = "server";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el nombre del usuario desde la sesión
$usuario = $_SESSION['nom'];

// Importar el cliente de MongoDB
require 'vendor/autoload.php'; // Asegúrate de incluir la ruta correcta al archivo autoload.php

// Crear una nueva conexión a MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");

// Seleccionar la base de datos y la colección en MongoDB
$collection = $client->registros->archivos;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $archivo = $_POST['delete_file'];
    $nombre_Carpeta = $_POST['nombre_Carpeta'];

    $ruta_archivo = "/var/www/servidor/archivos_compartidos/$nombre_Carpeta/$archivo";

    // Eliminar el archivo del sistema de archivos
    if (file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }

    // Eliminar el documento relacionado en MongoDB
    $collection->deleteOne(['nombre' => $archivo]);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Mis archivos</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/css/mis_archivos.css">
  <script>
    function toggleFiles(id) {
        var filesDiv = document.getElementById(id);
        if (filesDiv.style.display === "none") {
            filesDiv.style.display = "block";
        } else {
            filesDiv.style.display = "none";
        }
    }
  </script>
</head>
<body>
<header>
  <img src="/css/logo.png" class="logo">
  <div class="botones">
    <button class="compartir"><strong><a href="compartir_archivo.php">Compartir</a></strong></button>
    <button><strong><a href="mis_archivos.php">Mis archivos</a></strong></button>
    <button><strong><a href="grupos.php">Grupo</a></strong></button>
    <button class="nombre"><strong><?php echo $_SESSION['nom']?></strong></button>
  </div>
</header>
<div class="titulo">
  <h1>Mis archivos:</h1>
</div>
<hr class="linea_separacion">
<table>
  <tr>
    <th>Archivo</th>
    <th>Propietario</th>
    <th>Acciones</th>
  </tr>
<?php 

$sql = "SELECT g.ID_Carpeta, g.Nombre_Carpeta
        FROM Compartidos g
        INNER JOIN usuarioxcarpeta ug ON g.ID_Carpeta = ug.ID_Carpeta
        INNER JOIN usuario u ON ug.ID_USER = u.ID_USER
        WHERE u.USER_NAME = '$usuario'";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $id_Carpeta = $fila['ID_Carpeta'];
        $nombre_Carpeta = $fila['Nombre_Carpeta'];
        $ruta_carpetas = "/var/www/servidor/archivos_compartidos/$nombre_Carpeta";

        if (is_dir($ruta_carpetas)) {
            $archivos_carpeta = array_diff(scandir($ruta_carpetas), array('.', '..'));
            if (count($archivos_carpeta) > 1) {
                echo "<tr>";
                echo "<td colspan='3'><a href='javascript:void(0);' onclick='toggleFiles(\"carpeta_$id_Carpeta\")'><img src='/css/archivo.png' width='45' height='45'> $nombre_Carpeta</a></td>";
                echo "</tr>";
                echo "<tr id='carpeta_$id_Carpeta' style='display:none;'><td colspan='3'>";
                echo "<table>";
                foreach ($archivos_carpeta as $archivo) {
                    $documento = $collection->findOne(['nombre' => $archivo]);
                    if ($documento) {
                        $propietario = $documento['usuario'];
                        echo "<tr>";
                        echo "<td><img src='/css/foto.png' width='45' height='45'> <a href='descargar_compartido.php?file=$archivo'>$archivo</a></td>";
                        echo "<td>$propietario</td>";
                        echo "<td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='delete_file' value='$archivo'>
                                    <input type='hidden' name='id_Carpeta' value='$id_Carpeta'>
                                    <input type='hidden' name='nombre_Carpeta' value='$nombre_Carpeta'>
                                    <button type='submit'>Eliminar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    } else {
                        echo "<tr><td colspan='3'>No se encontró información sobre el propietario de $archivo.</td></tr>";
                    }
                }
                echo "</table>";
                echo "</td></tr>";
            } else {
                foreach ($archivos_carpeta as $archivo) {
                    $documento = $collection->findOne(['nombre' => $archivo]);
                    if ($documento) {
                        $propietario = $documento['usuario'];
                        echo "<tr>";
                        echo "<td><img src='/css/archivo.png' width='45' height='45'> <a href='descargar_compartido.php?file=$archivo'>$archivo</a></td>";
                        echo "<td>$propietario</td>";
                        echo "<td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='delete_file' value='$archivo'>
                                    <input type='hidden' name='id_Carpeta' value='$id_Carpeta'>
                                    <input type='hidden' name='nombre_Carpeta' value='$nombre_Carpeta'>
                                    <button type='submit'>Eliminar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    } else {
                        echo "<tr><td colspan='3'>No se encontró información sobre el propietario de $archivo.</td></tr>";
                    }
                }
            }
        } else {
            echo "<tr><td colspan='3'>No se encontraron archivos en tu carpeta.</td></tr>";
        }
    }
    $conn->close();
}
?>
</table>
</body>
</html>
