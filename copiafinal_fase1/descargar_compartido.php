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

// Verificar si se ha pasado el archivo como parámetro
if (!isset($_GET['file'])) {
    die("No se especificó ningún archivo.");
}

// Obtener el nombre del archivo desde el parámetro GET
$archivo = basename($_GET['file']);

// Consulta para obtener los grupos en los que está inscrito el usuario
$sql = "SELECT g.ID_Carpeta, g.Nombre_Carpeta
        FROM Compartidos g
        INNER JOIN usuarioxcarpeta ug ON g.ID_Carpeta = ug.ID_Carpeta
        INNER JOIN usuario u ON ug.ID_USER = u.ID_USER
        WHERE u.USER_NAME = '$usuario'";

$resultado = $conn->query($sql);

$archivo_encontrado = false;

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $nombre_Carpeta = $fila['Nombre_Carpeta'];
        $ruta_carpetas = "/var/www/servidor/archivos_compartidos/$nombre_Carpeta";

        // Verificar si el archivo existe en la carpeta del grupo
        $ruta_archivo = $ruta_carpetas . '/' . $archivo;
        if (file_exists($ruta_archivo)) {
            $archivo_encontrado = true;
            break;
        }
    }
}

$conn->close();

if (!$archivo_encontrado) {
    die("Archivo no encontrado o no tienes permisos para descargarlo.");
}

// Enviar encabezados para la descarga del archivo
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($ruta_archivo).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($ruta_archivo));

// Leer el archivo y enviarlo al navegador
readfile($ruta_archivo);
exit();
?>
