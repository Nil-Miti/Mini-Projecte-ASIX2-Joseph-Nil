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
        $sqlInsert = "INSERT INTO Compartidos (Carpeta) VALUES ('$titulo')";
        if ($conn->query($sqlInsert) === TRUE) {
            $idCarpeta = $conn->insert_id; // Obtiene el ID de la carpeta recién insertada

            // Actualiza la columna Carpeta en la tabla Usuario
            $sqlUpdate = "UPDATE usuario SET Carpeta = '$idCarpeta' WHERE ID_USER = '$usuarioID'";
            if ($conn->query($sqlUpdate) === TRUE) {
                echo "Registro insertado y actualizado con éxito.";
            } else {
                echo "Error al actualizar la carpeta en la tabla Usuario: " . $conn->error;
            }
        } else {
            echo "Error al insertar el registro en la tabla Compartidos: " . $conn->error;
        }
    }
    // Procesar la subida de archivos
    $uploadedFiles = [];

    foreach ($_FILES['archivos']['name'] as $key => $nombreArchivo) {
        $tempFile = $_FILES['archivos']['tmp_name'][$key];
        $targetFile = $uploadsDirectory . basename($nombreArchivo);

        if (move_uploaded_file($tempFile, $targetFile)) {
            $uploadedFiles[] = $targetFile;
        }
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir Carpeta</title>
</head>
<body>
    <h2>Compartir Carpeta</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required><br>

        <label for="titulo">Título (Nombre de la Carpeta):</label>
        <input type="text" name="titulo" required><br>

        <label for="usuario">Nombre de Usuario:</label>
        <input type="text" name="usuario" required><br>

        <label for="archivos">Subir Archivos:</label>
        <input type="file" name="archivos[]" multiple><br>

        <input type="submit" value="Compartir">
    </form>
</body>
</html>
