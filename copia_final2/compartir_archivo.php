<?php
session_start();

if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirige a la página de inicio de sesión o muestra un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

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
    $user = $_SESSION['nom'];

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
// Inserta el nombre de la carpeta en la tabla Compartidos
$sqlInsert = "INSERT INTO Compartidos (Nombre_Carpeta) VALUES ('$titulo')";
if ($conn->query($sqlInsert) === TRUE) {
    $idCarpeta = $conn->insert_id; // Obtiene el ID de la carpeta recién insertada

    // Inserta los valores en la tabla usuarioxcarpeta
    $insertUserCarpeta = "INSERT INTO usuarioxcarpeta (ID_USER, ID_Carpeta) VALUES ('$usuarioID', '$idCarpeta')";
    if ($conn->query($insertUserCarpeta) === TRUE) {
        // Éxito al insertar la relación usuario-carpeta
    } else {
        echo "Error al insertar relación usuario-carpeta: " . $conn->error;
    }



// Procesar la subida de archivos
    $uploadedFiles = [];

    foreach ($_FILES['file']['name'] as $key => $nombreArchivo) {
        $tempFile = $_FILES['file']['tmp_name'][$key];
        $targetFile = $uploadsDirectory . basename($nombreArchivo);

        if (move_uploaded_file($tempFile, $targetFile)) {
            $uploadedFiles[] = $targetFile;
        }
    }

    // Ejecutar el script Python y pasar el título como argumento
    $scriptPath = 'virustotal_compartir.py'; 
       foreach ($uploadedFiles as $archivo) {
        $command = "python3 $scriptPath $titulo $archivo";
        shell_exec($command);
    }

    // Puedes mostrar los archivos subidos (esto es solo un ejemplo)
    if (!empty($uploadedFiles)) {
        echo "<p>Archivos subidos:</p>";
        foreach ($uploadedFiles as $archivo) {
            echo "<p>{$archivo}</p>";
        }
    }
    $conn->close();
}
}
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Formulario Login</title>
</head>
<body>
<header style="margin-left: 1244px;">
  <img src="logo.png" class="logo">
  <div class="botones">
    <button><strong>Compartir</strong></button>
    <button><strong>Mis archivos</strong></button>
    <button><strong>Grupo</strong></button>
    <button><strong>name user</strong></button>
  </div>
</header>
<div class="formulari">
<form action='compartir_archivo.php' method='post' enctype='multipart/form-data'>
  <section class="form-login">
    <input type="file" name = "file[]">
      <img src="icono suma.png" class="icono-suma">      
<h1>Subir archivos</h1>
      <p>Selecciona tu carpeta o archivos deseados</p>
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
      <input class="button1" type="submit" name="submit" value="enviar">
      <input id="fileInput" class="button1" type="file" style="display: none;" onchange="handleFileSelect(event)">
    </div>
  </section>
</form>
</div>

<script>
  function openFileSelector() {
    document.getElementById('fileInput').click();
  }

  function handleFileSelect(event) {
    const file = event.target.files[0];
    // Obtener el párrafo donde se mostrará el nombre del archivo
    const paragraph = document.querySelector('.capçalera p');
    // Mostrar el nombre del archivo seleccionado en el párrafo
    paragraph.textContent = file.name;
    // Aquí puedes agregar la lógica para manejar el archivo seleccionado
    console.log('Archivo seleccionado:', file.name);
  }
</script>
</body>
</html>
