<?php
session_start();

// Verifica si se ha enviado el formulario de login
if (isset($_REQUEST['login'])) {
    // Conexión a la base de datos (modifica según tus credenciales)
    $servername = "localhost";
    $username = "admin";
    $password = "1234";
    $dbname = "server";

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("Conexión errónea: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM usuario WHERE USER_NAME = ? AND PASSW = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Corrige el error aquí: cambia el punto por una coma
    mysqli_stmt_bind_param($stmt, "ss", $_REQUEST['USER_NAME'], $_REQUEST['PASSW']);

    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        $_SESSION['id_usuario'] = 1;
        $_SESSION['nom'] = $_REQUEST['USER_NAME'];
        header("Location: ../index.html");
        exit; // Agrega un exit para evitar que el código siguiente se ejecute
    } else {
        $_SESSION['id_usuario'] = 0;
        echo "<h1>Usuario erróneo o contraseña erróneos</h1>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<img src="logo.png"  class="imagen2">
<div id="usuari">
    <h1 text>Inicia sesión</h1>
</div>
<div id="crear">
    <form action="login.php" method="post">
    <div id="usuari2">
        <input type="text" name="USER_NAME" placeholder="Nombre de usuario" required>
        <br>
    </div>
    <div id="usuari2">
        <input type="password" name="PASSW" placeholder="Contraseña" required>
    </div>
    <br>
    <button type="submit" name="login">iniciar sesion</button>
    </form>
</div>
<div id="usuari">
    <h1>¿Olvidaste tu contraseña?</h1>
</div>
    <img src="logo.png"  class="imagen">
</body>
</html>
