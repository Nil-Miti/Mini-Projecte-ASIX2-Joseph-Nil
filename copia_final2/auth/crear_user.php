<?php 
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
        $user = $_POST['USER_NAME'];
        $password = $_POST['PASSW'];

        // Inserta el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuario (USER_NAME, PASSW) VALUES ('$user', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "Usuario creado exitosamente";
        } else {
            echo "Error al crear usuario: " . $conn->error;
        }

        // Cierra la conexión
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/crear_user.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
</head>
<body>

<h2>Crear Usuario</h2>
<form action="crear_user.php" method="post">
    <label for="user">Usuario:</label>
    <input type="text" id="USER_NAME" name="USER_NAME" required><br>

    <label for="password">Contraseña:</label>
    <input type="password" id="PASSW" name="PASSW" required><br>

    <input type="submit" value="Crear Usuario">
</form>

</body>
</html>
	
