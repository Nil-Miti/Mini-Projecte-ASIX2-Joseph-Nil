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
        $user = $_POST['username'];
        $password = $_POST['password'];
	$email = $_POST['email'];
        // Inserta el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuario (USER_NAME, PASSW, email) VALUES ('$user', '$password', '$email')";

        if ($conn->query($sql) === TRUE) {
            echo "Usuario creado exitosamente";
        } else {
            echo "Error al crear usuario: " . $conn->error;
        }

        // Cierra la conexión
        $conn->close();
    }
?>
	
