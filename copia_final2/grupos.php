<?php
session_start();

if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirige a la página de inicio de sesión o muestra un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

// Conexión a la base de datos (modifica según tus credenciales)
$servername = "localhost";
$username = "admin";
$password = "1234";
$dbname = "server";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtiene el nombre del usuario desde la sesión
$usuario = $_SESSION['nom'];

// Realiza una consulta para obtener el grupo del usuario
$query = "SELECT GRUPO FROM usuario WHERE USER_NAME = '$usuario'";
$result = $conn->query($query);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $id_grupo = $row['GRUPO'];

    // Realiza una consulta para obtener el nombre del grupo
    $query_grupo = "SELECT nombre FROM grupos WHERE ID = '$id_grupo'";
    $result_grupo = $conn->query($query_grupo);

    if ($result_grupo->num_rows == 1) {
        $row_grupo = $result_grupo->fetch_assoc();
        $nombre_grupo = $row_grupo['nombre'];

        // Construye la ruta completa del directorio
        $ruta_carpeta = "/var/www/servidor/grupos/$nombre_grupo/";

        // Procesar la subida de archivos
        $uploadsDirectory = $ruta_carpeta; // Se corrige la ruta para subir los archivos
        $uploadedFiles = [];

        foreach ($_FILES['file']['tmp_name'] as $key => $tempFile) {
            $nombreArchivo = $_FILES['file']['name'][$key];
            $targetFile = $uploadsDirectory . basename($nombreArchivo);

            if (move_uploaded_file($tempFile, $targetFile)) {
                $uploadedFiles[] = $targetFile;
            }
        }

        $scriptPath = '/var/www/servidor/virustotal.py';
       foreach ($uploadedFiles as $file) {
            $command = "python3 $scriptPath $nombre_grupo";
            shell_exec($command);
        }

        // Obtiene la lista de archivos en el directorio
        $archivos = scandir($ruta_carpeta);

        // Muestra el nombre de la carpeta como un enlace
        echo "<p><a href='javascript:void(0);' onclick='mostrarArchivos();'>$nombre_grupo</a></p>";

        // Container para mostrar los archivos (inicialmente oculto)
        echo "<div id='archivos-container' style='display: none;'>";

        // Muestra la lista de archivos
       echo "<ul>";
	foreach ($archivos as $archivo) {
    // Excluye los directorios "." y ".."
    	if ($archivo != "." && $archivo != "..") {
        // Crea un enlace de descarga para cada archivo
        echo "<li><a href='descargar.php?archivo=$archivo&grupo=$nombre_grupo'>$archivo</a></li>";
    }
}
echo "</ul>";

        // Formulario para cargar archivos
        echo "<form action='grupos.php' method='post' enctype='multipart/form-data'>";
        echo "<label for='file'>Subir archivo:</label>";
        echo "<input type='file' name='file[]' id='file' multiple>"; // Se cambia el nombre del input a 'file[]' para permitir múltiples archivos
        echo "<input type='submit' value='Subir'>";
        echo "</form>";

        echo "</div>";
    } else {
        // El grupo no existe, maneja el error
        echo "Error: Grupo no encontrado.";
    }
} else {
    // No se encontró el usuario, maneja el error
    echo "Error: Usuario no encontrado.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/crear_user.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupos</title>
    <script>
        function mostrarArchivos() {
            var archivosContainer = document.getElementById("archivos-container");
            archivosContainer.style.display = "block";
        }
    </script>
</head>
<body>

<h2>Grupos</h2>

</body>
</html>
