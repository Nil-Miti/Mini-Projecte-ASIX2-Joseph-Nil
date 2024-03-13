<?php
session_start();

// Verificar si el usuario está autenticado y tiene permisos
if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirigir a la página de inicio de sesión o mostrar un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

// Conexión a la base de datos (modificar según tus credenciales)
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

// Variable para almacenar el contenido que se mostrará en el div
$contenido = '';

// Consulta para obtener los grupos en los que está inscrito el usuario
$sql = "SELECT g.ID_Carpeta, g.Nombre_Carpeta
        FROM Compartidos g
        INNER JOIN usuarioxcarpeta ug ON g.ID_Carpeta = ug.ID_Carpeta
        INNER JOIN usuario u ON ug.ID_USER = u.ID_USER
        WHERE u.USER_NAME = '$usuario'";

$resultado = $conn->query($sql);


?>
<!DOCTYPE html>
<html>
<head>
  <title>Título de la página</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="mis_archivos.css">
</head>
<body>
<header>
  <img src="logo.png" class="logo">
  <div class="botones">
    <button><strong><a href="compartir_archivo.php">Compartir</strong></button></a>
    <button><strong><a href="mis_archivos.php">Mis archivos</strong></button></a>
    <button><strong><a href="Grupos.php">Grupo</strong></button></a>
    <button><strong><?php echo $_SESSION['nom']?></strong></button>
  </div>
</header>

<hr class="linea_separacion">
<?php 

if ($resultado->num_rows > 0) {
    // Mostrar los grupos y permitir al usuario interactuar con ellos
    while ($fila = $resultado->fetch_assoc()) {
        $id_Carpeta = $fila['ID_Carpeta'];
        $nombre_Carpeta = $fila['Nombre_Carpeta'];
        echo "<h2>Carpeta: $nombre_Carpeta</h2>";

        // Directorio donde se guardarán los archivos del grupo
        $ruta_carpetas = "/var/www/servidor/archivos_compartidos/$nombre_Carpeta";

        // Mostrar los archivos dentro del grupo
        if (is_dir($ruta_carpetas)) {
            $archivos_carpeta = scandir($ruta_carpetas);
            echo "<h3>Archivos compartidos:</h3>";
            echo "<ul>";
            foreach ($archivos_carpeta as $archivo) {
                if ($archivo != '.' && $archivo != '..') {
                    // Agregar enlace de descarga para cada archivo
                    echo "<li><a href='descargar_compartido.php?file=$nombre_Carpeta/$archivo'>$archivo</a></li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p>No se encontraron archivos en tu carpeta.</p>";
        }
    }
    $conn->close();
}?>

</body>
</html>
