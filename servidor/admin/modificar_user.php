<?php
// Verificar si se proporciona un ID de usuario válido en la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];
} else {
    // Si no se proporciona un ID válido, redireccionar o mostrar un mensaje de error
    header("Location: error.php");
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

// Obtener los datos del usuario desde la base de datos
$sql_usuario = "SELECT * FROM usuario WHERE ID_USER = $id_usuario";
$result_usuario = $conn->query($sql_usuario);

if ($result_usuario->num_rows > 0) {
    $usuario = $result_usuario->fetch_assoc();
} else {
    // Si no se encuentra el usuario, redireccionar o mostrar un mensaje de error
    header("Location: error.php");
    exit();
}

// Obtener los departamentos asignados al usuario desde la base de datos
$sql_departamentos_usuario = "SELECT ID_GRUPO FROM usuarioxgrupo WHERE ID_USER = $id_usuario";
$result_departamentos_usuario = $conn->query($sql_departamentos_usuario);

$departamentos_usuario = array();
if ($result_departamentos_usuario->num_rows > 0) {
    while ($row = $result_departamentos_usuario->fetch_assoc()) {
        $departamentos_usuario[] = $row['ID_GRUPO'];
    }
}

// Consultar todos los departamentos disponibles
$sql_departamentos_disponibles = "SELECT * FROM grupos";
$result_departamentos_disponibles = $conn->query($sql_departamentos_disponibles);

$departamentos_disponibles = array();
if ($result_departamentos_disponibles->num_rows > 0) {
    while ($row = $result_departamentos_disponibles->fetch_assoc()) {
        $departamentos_disponibles[] = $row;
    }
}

// Procesar los datos del formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si se envió una solicitud de eliminación
    if (isset($_POST['eliminar'])) {
        // Eliminar el usuario de la base de datos
        $sql_eliminar_usuario = "DELETE FROM usuario WHERE ID_USER = $id_usuario"; 
        if ($conn->query($sql_eliminar_usuario) === TRUE) {
            // Redireccionar a una página de confirmación o realizar otras acciones necesarias
            echo "El usuario ha sido eliminado correctamente.";
            exit();
        } else {
            echo "Error al intentar eliminar el usuario: " . $conn->error;
        }
    } else {
        // Actualizar el estado del usuario
        $nuevo_estado = $_POST['estado'];
        $sql_actualizar_estado = "UPDATE usuario SET estado = '$nuevo_estado' WHERE ID_USER = $id_usuario";
        $conn->query($sql_actualizar_estado);
        
        // Actualizar los departamentos asignados al usuario
        if (isset($_POST['eliminar_departamentos'])) {
            // Limpiar los departamentos existentes antes de agregar los nuevos
            $sql_eliminar_departamentos = "DELETE FROM usuarioxgrupo WHERE ID_USER = $id_usuario";
            $conn->query($sql_eliminar_departamentos);
            
            // Insertar los nuevos departamentos seleccionados
            foreach ($_POST['departamentos'] as $id_departamento) {
                $sql_insertar_departamento = "INSERT INTO usuarioxgrupo (ID_USER, ID_GRUPO) VALUES ($id_usuario, $id_departamento)";
                $conn->query($sql_insertar_departamento);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="modificar_user.css"/>
</head>
<body>
<header>
  <img src="../css/logo.png" class="logo">
  <div class="botones">
    <button class="compartir"><a href="../compartir_archivo.php"><strong>Compartir</strong></a></button>
    <button class="mis-archivos"><a href="../mis_archivos.php"><strong>Mis archivos</strong></a></button>
    <button class="grupo"><a href="../grupos.php"><strong>Grupo</strong></a></button>
    <button class="nombre"><strong><?php echo $_SESSION['nom']?></strong></button>
  </div>
</header>

<div class="box1">
    <h1>Modificar Usuario</h1>
    <div class="box_form">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id_usuario; ?>">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <option value="autorizado" <?php if ($usuario['estado'] == 'autorizado') echo "selected"; ?>>Autorizado</option>
                <option value="no_autorizado" <?php if ($usuario['estado'] == 'no_autorizado') echo "selected"; ?>>No autorizado</option>
                <option value="espera" <?php if ($usuario['estado'] == 'espera') echo "selected"; ?>>En espera</option>
            </select>
            <!-- Agregar otros campos para modificar según sea necesario -->

            <h2>Departamentos:</h2>
            <ul>
                <?php foreach ($departamentos_disponibles as $departamento) : ?>
                    <li>
                        <input type="checkbox" name="departamentos[]" value="<?php echo $departamento['ID_GRUPO']; ?>" <?php if (in_array($departamento['ID_GRUPO'], $departamentos_usuario)) echo "checked"; ?>>
                        <?php echo $departamento['NOMBRE_GRUPO']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- Botón para eliminar el usuario -->
            <button type="submit" name="eliminar" style="background-color: red; color: white;" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');">Eliminar Usuario</button>
            <button type="submit" name="eliminar_departamentos[]">Agregar/Quitar</button>
	   <p> Antes de eliminar un usuario debemos quitarlo de todos los departamentos</p>
        </form>
    </div>
</div>

</body>
</html>
