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

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grupos</title>
<link rel="stylesheet" href="/css/grupos.css">
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
<h1>Grupos</h1>
</div>
<div class="nom">
<p><b>Nom:<b></p>
<hr class='linea_separacion'>
</div>
<table>
<?php
if ($resultado->num_rows > 0) {
    // Mostrar los grupos y permitir al usuario interactuar con ellos
    while ($fila = $resultado->fetch_assoc()) {
        $id_grupo = $fila['ID_GRUPO'];
        $nombre_grupo = $fila['NOMBRE_GRUPO'];
        echo "<td><img src='/css/archivo.png' width='55' height='55'>
        <a href='/mostrar_archivos.php?id_grupo=$id_grupo'>$nombre_grupo</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='1'>El usuario no está inscrito en ningún grupo.</td></tr>";
}

$conn->close();
?>
</table>

</body>
</html>
