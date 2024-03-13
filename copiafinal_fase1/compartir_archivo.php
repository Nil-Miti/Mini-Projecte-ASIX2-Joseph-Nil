<?php
session_start();
if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirige a la página de inicio de sesión o muestra un mensaje de error
    header("Location: /auth/login.php");
    exit();
}
$_SESSION['nom'];
echo $_SESSION['nom'];
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

    // Variable del usuario que ha iniciado sesión
    // Recibe los datos del formulario
   $email = $_POST['email'];
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
        mkdir($uploadsDirectory);

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
echo $_SESSION['nom'];
    // Procesar la subida de carpetas si se proporciona
 $uploadedFolders = [];
            // Procesar la subida de carpetas si se proporciona
            foreach ($_FILES['folderInput']['name'] as $key => $nombreCarpeta) {
		$tempFolder = $_FILES['folderInput']['tmp_name'][$key];
		$targetFolder = $uploadsDirectory . basename($nombreCarpeta);
                if (move_uploaded_file($tempFolder, $targetFolder)) {
			$uploadedFolders[] = $targetFolder;
                    // Realizar cualquier procesamiento adicional aquí
                } else {
                    echo "Error al subir la carpeta.";
    }
$command = "python3 virustotal_compartir.py $titulo $user";
        shell_exec($command);
                echo "La carpeta se ha subido con éxito.";
}
    // Procesar la subida de archivos si se proporciona
    if (!empty($_FILES['file']['name'])) {
        foreach ($_FILES['file']['name'] as $key => $nombreArchivo) {
            $tempFile = $_FILES['file']['tmp_name'][$key];
            $targetFile = $uploadsDirectory . basename($nombreArchivo);

            if (move_uploaded_file($tempFile, $targetFile)) {
	echo "La archivo se ha subido con exito";            
} 
        }

        // Ejecutar el script Python y pasar el título como argumento
        $command = "python3 virustotal_compartir.py $titulo $user";
        shell_exec($command);
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Subir Carpeta/Archivo</title>
  <link rel="stylesheet" href="compartir_archivo.css"/>
</head>
<body>
<header style="margin-left: 1244px;">
  <img src="logo.png" class="logo">
  <div class="botones">
    <button><strong><a href="compartir_archivo.php">Compartir</strong></button></a>
    <button><strong><a href="mis_archivos.php">Mis archivos</strong></button></a>
    <button><strong><a href="Grupos.php">Grupo</strong></button></a>
    <button><strong><?php echo $_SESSION['nom']?></strong></button>
  </div>
</header>
<div class="formulari">
<div class="capçalera">
  <button type="submit"></button>
<form action='compartir_archivo.php' method='post' enctype='multipart/form-data'>
  <section class="form-login">
      <img src="icono suma.png" class="icono-suma">
    </div>
    <input type="file" name = "file[]">
  <input type="file" id="folderInput" name="folderInput[]" webkitdirectory directory multiple>
    <hr class="linea_separacion"/>
    <div class="contenido">
      <black>Enviar email a:</black>
      <input class="caja" type="text" id="email" name="email">
      <hr class="hr"/>
      <black>Titulo:</black>
      <input class="caja" type="text" name="titulo" id="titulo">
      <hr class="hr"/>
      <black>Nombre de usuario:</black>
      <input class="caja" type="text" name="usuario" id="usuario">
      <hr class="hr"/>
      <input class="button1" type="submit" name="submit" value="Enviar">
    </div>
  </section>
</form>
</div>


<script>
  document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    fetch('upload.php', {
      method: 'POST',
      body: formData
    }).then(response => {
      // Manejar la respuesta del servidor si es necesario
    }).catch(error => {
      console.error('Error en la solicitud:', error);
    });
  });
</script>
</body>
</html>
