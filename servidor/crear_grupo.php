<?php
session_start();

if (!(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] == 1)) {
    // Redirige a la página de inicio de sesión o muestra un mensaje de error
    header("Location: /auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Obtiene los datos del formulario
    $nombre = $_POST['nombre'];

    // Verifica si el grupo ya existe
    $sql_check_grupo = "SELECT * FROM grupos WHERE NOMBRE_GRUPO = ?";
    $stmt_check_grupo = $conn->prepare($sql_check_grupo);
    $stmt_check_grupo->bind_param("s", $nombre);
    $stmt_check_grupo->execute();
    $result_check_grupo = $stmt_check_grupo->get_result();

    if ($result_check_grupo->num_rows > 0) {
        echo "El grupo ya existe";
    } else {
        // Inserta el nuevo grupo en la tabla 'grupos'
        $sql_insert_grupo = "INSERT INTO grupos (NOMBRE_GRUPO) VALUES (?)";
        $stmt_insert_grupo = $conn->prepare($sql_insert_grupo);
        $stmt_insert_grupo->bind_param("s", $nombre);

        if ($stmt_insert_grupo->execute()) {
            echo "Grupo creado con éxito";

            // Obtiene el ID del grupo recién insertado
            $id_grupo = $conn->insert_id;
            // Obtiene el nombre del usuario desde la sesión
            $usuario = $_SESSION['nom'];
            // Crea la carpeta en /var/www/servidor/grupos
            $ruta_carpeta = "/var/www/servidor/grupos/". $nombre;
            if (!file_exists($ruta_carpeta)) {
                mkdir ($ruta_carpeta);
            }
        } else {
            echo "Error al crear el grupo";
        }

        // Cierra la conexión
        $stmt_insert_grupo->close();
    }

    // Cierra la conexión
    $stmt_check_grupo->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/crear_user.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Grupo</title>
</head>
<body>

<h2>Crear Grupo</h2>
<form action="crear_grupo.php" method="post">
    <label for="nombre">Crear nuevo grupo:</label>
    <input type="text" id="nombre" name="nombre" required><br>
    <input type="submit" value="Crear grupo">
</form>
</body>
</html>
