<?php
session_start();
if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirige a la página de inicio de sesión o muestra un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

$user = $_SESSION['nom'];

// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos (modifica según tus credenciales)
    $servername = "localhost";
    $username = "admin";
    $password = "1234";
    $dbname = "server";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Recibe los datos del formulario
    $titulo = $_POST['titulo'];
    $usuario = $_POST['usuario'];

    $userQuery = "SELECT ID_USER FROM usuario WHERE USER_NAME = '$usuario'";
    $result = $conn->query($userQuery);

    if ($result->num_rows === 0) {
        echo '<script>alert("El usuario no existe. Solicitud cancelada.");</script>';
        header("Location: compartir_archivo.php");
        exit();
    }

    // Obtiene el ID del usuario
    $row = $result->fetch_assoc();
    $usuarioID = $row['ID_USER'];

    // Crea un directorio para la carpeta compartida
    $uploadsDirectory = '/var/www/servidor/archivos_compartidos/' . $titulo . '/';

    if (!is_dir($uploadsDirectory)) {
        mkdir($uploadsDirectory, 0777, true);

        // Inserta el nombre de la carpeta en la tabla Compartidos
        $sqlInsert = "INSERT INTO  Compartidos (Nombre_Carpeta) VALUES ('$titulo')";
        if ($conn->query($sqlInsert) === TRUE) {
            $idCarpeta = $conn->insert_id; // Obtiene el ID de la carpeta recién insertada

            // Inserta los valores en la tabla usuarioxcarpeta
            $insertUserCarpeta = "INSERT INTO usuarioxcarpeta (ID_USER, ID_Carpeta) VALUES ('$usuarioID', '$idCarpeta')";
            if ($conn->query($insertUserCarpeta) === TRUE) {
                // Éxito al insertar la relación usuario-carpeta
            } else {
                echo "Error al insertar relación usuario-carpeta: " . $conn->error;
            }
        }
    }

    $uploadSuccess = false;

    // Procesar la subida de carpetas si se proporciona
    if (!empty($_FILES['folderInput']['name'][0])) {
        foreach ($_FILES['folderInput']['name'] as $key => $nombreCarpeta) {
            $tempFolder = $_FILES['folderInput']['tmp_name'][$key];
            $targetFolder = $uploadsDirectory . basename($nombreCarpeta);
            if (move_uploaded_file($tempFolder, $targetFolder)) {
                $uploadSuccess = true;
                // Realizar cualquier procesamiento adicional aquí
            } else {
                echo "Error al subir la carpeta.";
            }
        }
    }

    // Procesar la subida de archivos si se proporciona
    if (!empty($_FILES['file']['name'][0])) {
        foreach ($_FILES['file']['name'] as $key => $nombreArchivo) {
            $tempFile = $_FILES['file']['tmp_name'][$key];
            $targetFile = $uploadsDirectory . basename($nombreArchivo);
            if (move_uploaded_file($tempFile, $targetFile)) {
                $uploadSuccess = true;
                // Realizar cualquier procesamiento adicional aquí
                echo "El archivo se ha subido con éxito";
            } else {
                echo "Error al subir el archivo.";
            }
        }
    }

    // Ejecutar el script Python solo si se subió al menos un archivo o carpeta
    if ($uploadSuccess) {
        $command = escapeshellcmd("python3 virustotal_compartir.py " . escapeshellarg($titulo) . " " . escapeshellarg($user));
        shell_exec($command);
        echo "El script de análisis se ha ejecutado con éxito.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Subir Carpeta/Archivo</title>
    <link rel="stylesheet" href="/css/compartir_archivos.css"/>
</head>
<body>
<header>
    <img src="/css/logo.png" class="logo">
    <div class="botones">
        <button class="compartir"><a href="compartir_archivo.php"><strong>Compartir</strong></button></a>
        <button class="mis-archivos"><a href="mis_archivos.php"><strong>Mis archivos</strong></button></a>
        <button class="grupo"><a href="grupos.php"><strong>Grupo</strong></button></a>
        <button class="nombre"><strong><?php echo $_SESSION['nom']?></strong></button>
    </div>
</header>
<div class="formulari">
    <div class="capçalera">
        <form action='compartir_archivo.php' method='post' enctype='multipart/form-data'>
            <button type="submit"></button>
            <section class="form-login">
                <input type="file" name="file[]" multiple>
                <input type="file" id="folderInput" name="folderInput[]" webkitdirectory directory multiple> 
            </div>
            <hr class="linea_separacion"/>
            <div class="contenido">
                <black>Enviar email a:</black>
                <input class="caja" type="text" id="email" name="email">
                <hr/>
                <black>Titulo:</black>
                <input class="caja" type="text" name="titulo" id="titulo">
                <hr/>
                <black>Nombre de usuario:</black>
                <input class="caja" type="text" name="usuario" id="usuario">
                <hr/>
                <input class="button1" type="submit" name="submit" value="Enviar">
            </div>
        </section>
    </form>
</div>
</body>
</html>
