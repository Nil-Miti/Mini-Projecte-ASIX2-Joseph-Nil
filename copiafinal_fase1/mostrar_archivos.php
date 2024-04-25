<?php
session_start();
// Verificar si el usuario está autenticado y tiene permisos
if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirigir a la página de inicio de sesión o mostrar un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

// Verificar si se ha proporcionado un ID de grupo válido
if (!isset($_GET['id_grupo'])) {
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

$id_grupo = $_GET['id_grupo'];

// Obtener el nombre del usuario desde la sesión
$usuario = $_SESSION['nom'];
// Consulta para obtener el nombre del grupo
$sql_grupo = "SELECT NOMBRE_GRUPO FROM grupos WHERE ID_GRUPO = $id_grupo";
$resultado_grupo = $conn->query($sql_grupo);
$nombre_grupo = $resultado_grupo->fetch_assoc()['NOMBRE_GRUPO'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES['file']['name'])) {
    // Directorio donde se guardarán los archivos subidos
    $uploadsDirectory = '/var/www/servidor/grupos/' . $nombre_grupo . '/';

    if (!empty($_FILES['file']['name'])) {
        foreach ($_FILES['file']['name'] as $key => $nombreArchivo) {
            $tempFile = $_FILES['file']['tmp_name'][$key];
            $targetFile = $uploadsDirectory . basename($nombreArchivo);

            if (move_uploaded_file($tempFile, $targetFile)) {
	echo "La archivo se ha subido con exito";            
} 
        }

        // Ejecutar el script Python y pasar el título como argumento
        $command = "python3 virustotal.py $nombre_grupo $usuario";
        shell_exec($command);
    }
}
// Directorio donde se guardarán los archivos del grupo
$ruta_grupo = "/var/www/servidor/grupos/$nombre_grupo";
// Mostrar los archivos dentro del grupo
if (is_dir($ruta_grupo)) {
    $archivos_grupo = scandir($ruta_grupo);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Archivos del grupo: <?php echo $nombre_grupo; ?></title>
        <link rel="stylesheet" href="/css/mostrar_archivos.css">
    </head>
    <body>
    <header>
        <img src="/css/logo.png" class="logo">
        <div class="botones">
            <button class="compartir"><a href="/compartir_archivo.php"><strong>Compartir</strong></a></button>
            <button class="mis-archivos"><a href="/mis_archivos.php"><strong>Mis archivos</strong></a></button>
            <button class="grupo"><a href="/grupos.php"><strong>Grupo</strong></a></button>
            <button class="nombre"><strong><?php echo $_SESSION['nom']?></strong></button>
        </div>
    </header>
    <div class="titulo">
        <h1>Grupo > <?php echo $nombre_grupo; ?></h1>
    </div>
    <div class="nom">
        <p><b>Nom:</b></p>
        <hr class='linea_separacion'>
    </div>
    <div class="archivos">
        <ul>
            <?php foreach ($archivos_grupo as $archivo) {
                if ($archivo != '.' && $archivo != '..') {
                    // Agregar enlace de descarga para cada archivo
                    echo "<li><img src='/css/foto.png' width='45' height='45'><a href='descargar.php?file=$nombre_grupo/$archivo'>$archivo</a></li>";
                }
            } ?>
        </ul>
    </div>
    <div class="subida">
        <h2>Subir Archivos</h2>
        <form action="mostrar_archivos.php?id_grupo=<?php echo $id_grupo; ?>" method="POST" enctype="multipart/form-data">
        <input type="file" name="file[]" multiple>
        <input type="submit" value="Subir archivos">
    </form>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "<p>No se encontraron archivos en el grupo.</p>";
}
$conn->close();
?>
