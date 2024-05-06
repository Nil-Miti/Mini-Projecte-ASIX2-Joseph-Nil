<?php
session_start();

// Verificar si el usuario está autenticado y tiene permisos
if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirigir a la página de inicio de sesión o mostrar un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

// Conexión a la base de datos MySQL (modificar según tus credenciales)
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
$collection = $client->registros->archivos; // Reemplaza "database_name" y "collection_name" con tus nombres reales

?>

<!DOCTYPE html>
<html>
<head>
  <title>Título de la página</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/css/mis_archivos.css">
</head>
<body>
<header>
  <img src="/css/logo.png" class="logo">
  <div class="botones">
    <button><strong><a href="compartir_archivo.php">Compartir</strong></button></a>
    <button><strong><a href="mis_archivos.php">Mis archivos</strong></button></a>
    <button><strong><a href="grupos.php">Grupo</strong></button></a>
    <button><strong><?php echo $_SESSION['nom']?></strong></button>
  </div>
</header>
<div class="titulo">
<h1>Mis archivos: </h1>
</div>
<div class="nom">
<p><b>Nom: </b></p>
<p class= "propietari"><b>Propietari: </b></p>
<hr class="linea_separacion">
</div>

<?php 

// Consulta para obtener los grupos en los que está inscrito el usuario
$sql = "SELECT g.ID_Carpeta, g.Nombre_Carpeta
        FROM Compartidos g
        INNER JOIN usuarioxcarpeta ug ON g.ID_Carpeta = ug.ID_Carpeta
        INNER JOIN usuario u ON ug.ID_USER = u.ID_USER
        WHERE u.USER_NAME = '$usuario'";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    // Mostrar los grupos y permitir al usuario interactuar con ellos
    while ($fila = $resultado->fetch_assoc()) {
        $id_Carpeta = $fila['ID_Carpeta'];
        $nombre_Carpeta = $fila['Nombre_Carpeta'];
        // Directorio donde se guardarán los archivos del grupo
        $ruta_carpetas = "/var/www/servidor/archivos_compartidos/$nombre_Carpeta";

        // Mostrar los archivos dentro del grupo
        echo "<div class='archivos'>";
        if (is_dir($ruta_carpetas)) {
            $archivos_carpeta = scandir($ruta_carpetas);
            foreach ($archivos_carpeta as $archivo) {
                if ($archivo != '.' && $archivo != '..') {
                    // Buscar en MongoDB el nombre del archivo y recuperar el usuario que lo creó
                    $documento = $collection->findOne(['nombre' => $archivo]);
                    if ($documento) {
                        $propietario = $documento['usuario'];
                        // Agregar enlace de descarga para cada archivo
                        echo "<li><img src='/css/foto.png' width='45' height='45'><a href='descargar_compartido.php?file=$archivo'>$archivo</a>";
                        // Mostrar el propietario del archivo
                        echo "<p class='resultado'>$propietario</p>";
                    } else {
                        echo "<p>No se encontró información sobre el propietario de $archivo.</p>";
                    }
                }
            }
        } else {
            echo "<p>No se encontraron archivos en tu carpeta.</p>";
        }
    }
    $conn->close();
}

?>

</body>
</html>
