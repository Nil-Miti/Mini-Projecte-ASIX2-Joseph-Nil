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

// Consulta para obtener los grupos en los que está inscrito el usuario
$sql = "SELECT g.ID_GRUPO, g.NOMBRE_GRUPO
        FROM grupos g
        INNER JOIN usuarioxgrupo ug ON g.ID_GRUPO = ug.ID_GRUPO
        INNER JOIN usuario u ON ug.ID_USER = u.ID_USER
        WHERE u.USER_NAME = '$usuario'";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    // Mostrar los grupos y permitir al usuario interactuar con ellos
    while ($fila = $resultado->fetch_assoc()) {
        $id_grupo = $fila['ID_GRUPO'];
        $nombre_grupo = $fila['NOMBRE_GRUPO'];
        echo "<h2>Grupo: $nombre_grupo</h2>";

        // Directorio donde se guardarán los archivos del grupo
        $ruta_grupo = "/var/www/servidor/grupos/$nombre_grupo/";

        // Mostrar los archivos dentro del grupo
        if (is_dir($ruta_grupo)) {
            $archivos_grupo = scandir($ruta_grupo);
            echo "<h3>Archivos del grupo:</h3>";
            echo "<ul>";
            foreach ($archivos_grupo as $archivo) {
                if ($archivo != '.' && $archivo != '..') {
                    // Agregar enlace de descarga para cada archivo
                    echo "<li><a href='$ruta_grupo/$archivo' download>$archivo</a></li>";
                }
            }
            echo "</ul>";
        } else {
            echo "No se encontraron archivos en el grupo.";
        }
    }
    // Mostrar el formulario de subida de archivos
    echo "<h3>Subir Archivo:</h3>";
    echo "<form action='grupos.php' method='post' enctype='multipart/form-data'>";
    echo "<select name='id_grupo'>";
    // Rebobinar el puntero de resultados para volver a recorrer los grupos
    mysqli_data_seek($resultado, 0);
    while ($fila = $resultado->fetch_assoc()) {
        $id_grupo = $fila['ID_GRUPO'];
        $nombre_grupo = $fila['NOMBRE_GRUPO'];
        echo "<option value='$nombre_grupo'>$nombre_grupo</option>";
    }
    echo "</select>";
    echo "<input type='file' name='file[]'>";
    echo "<input type='submit' value='Subir archivo' name='submit'>";
    echo "</form>";

    // Procesar la subida de archivos
    if (isset($_FILES['file'])) {
        $nombre_grupo = $_POST['id_grupo'];
        $ruta_grupo = "/var/www/servidor/grupos/$nombre_grupo/";
        $uploadsDirectory = $ruta_grupo;
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
    }
} else {
    echo "El usuario no está inscrito en ningún grupo.";
}

$conn->close();
?>
