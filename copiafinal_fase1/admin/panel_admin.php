<?php
session_start();

// Verificar si el usuario está autenticado y tiene permisos
if (!(isset($_SESSION['id_usuario']) && ($_SESSION['nom'] == 'joseph' || $_SESSION['nom'] == 'nil'))) {
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

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $departamento = $_POST['departamento'];

    if (isset($_POST['accept'])) {
        $estado = 'autorizado';
    } elseif (isset($_POST['reject'])) {
        $estado = 'no_autorizado';
    }

    // Actualizar el estado del usuario en la tabla usuario
    $sql_usuario = "UPDATE usuario SET estado = '$estado' WHERE ID_USER = $user_id";
    if ($conn->query($sql_usuario) === TRUE) {
        echo "Estado actualizado exitosamente";
    } else {
        echo "Error al actualizar el estado: " . $conn->error;
    }

    // Obtener el ID del grupo seleccionado
    $sql_grupo = "SELECT ID_GRUPO FROM grupos WHERE NOMBRE_GRUPO = '$departamento'";
    $result_grupo = $conn->query($sql_grupo);
    if ($result_grupo->num_rows > 0) {
        $row_grupo = $result_grupo->fetch_assoc();
        $grupo_id = $row_grupo['ID_GRUPO'];

        // Verificar si ya existe una relación usuario-grupo en la tabla usuarioxgrupo
        $sql_relacion = "SELECT * FROM usuarioxgrupo WHERE ID_USER = $user_id";
        $result_relacion = $conn->query($sql_relacion);

        if ($result_relacion->num_rows > 0) {
            // Actualizar la relación usuario-grupo en la tabla usuarioxgrupo
            $sql_actualizar_relacion = "UPDATE usuarioxgrupo SET ID_GRUPO = $grupo_id WHERE ID_USER = $user_id";
            if ($conn->query($sql_actualizar_relacion) === TRUE) {
                echo "Relación usuario-grupo actualizada exitosamente";
            } else {
                echo "Error al actualizar la relación usuario-grupo: " . $conn->error;
            }
        } else {
            // Insertar una nueva relación usuario-grupo en la tabla usuarioxgrupo
            $sql_insertar_relacion = "INSERT INTO usuarioxgrupo (ID_USER, ID_GRUPO) VALUES ($user_id, $grupo_id)";
            if ($conn->query($sql_insertar_relacion) === TRUE) {
                echo "Relación usuario-grupo insertada exitosamente";
            } else {
                echo "Error al insertar la relación usuario-grupo: " . $conn->error;
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
    <title>Panel de Administrador</title>
</head>
<body>
    <h2>Panel de Administrador</h2>
    <table>
        <tr>
            <th>Nombre de Usuario</th>
            <th>Correo Electrónico</th>
            <th>Asignar Departamento</th>
            <th>Acción</th>
        </tr>
        <?php
        // Mostrar los usuarios en el panel de administrador
        $sql_usuarios = "SELECT ID_USER, USER_NAME, email FROM usuario WHERE estado = 'espera'";
        $result_usuarios = $conn->query($sql_usuarios);

        if ($result_usuarios->num_rows > 0) {
            // Consulta SQL para obtener los departamentos disponibles
            $sql_departamentos = "SELECT NOMBRE_GRUPO FROM grupos";
            $result_departamentos = $conn->query($sql_departamentos);

            while ($row = $result_usuarios->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["USER_NAME"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>
                        <form action='panel_admin.php' method='post'>
                            <input type='hidden' name='user_id' value='" . $row["ID_USER"] . "'>
                            <select name='departamento'>";

                // Construir las opciones del select con los departamentos obtenidos
                if ($result_departamentos->num_rows > 0) {
                    while ($row_departamento = $result_departamentos->fetch_assoc()) {
                        echo "<option value='" . $row_departamento["NOMBRE_GRUPO"] . "'>" . $row_departamento["NOMBRE_GRUPO"] . "</option>";
                    }
                }

                echo "</select>
                      </td>";
                echo "<td>
                            <input type='submit' name='accept' value='Aceptar'>
                            <input type='submit' name='reject' value='Rechazar'>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay usuarios en espera</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
