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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="panel_admin.css"/>
</head>
<body>
<header>
  <img src="logo.png" class="logo">
  <div class="botones">
    <button class="compartir"><a href="../compartir_archivo.php"><strong>Compartir</strong></a></button>
    <button class="mis-archivos"><a href="../mis_archivos.php"><strong>Mis archivos</strong></a></button>
    <button class="grupo"><a href="../grupos.php"><strong>Grupo</strong></a></button>
    <button class="nombre"><strong><?php echo $_SESSION['nom']?></strong></button>
  </div>
</header>

<h1>Panel de Administrador</h1>
<div class="box1">
    <div class="box_form">
        <h2 class="h2">Usuarios Registrados</h2>
        <div class="acciones">
        <!-- Barra de búsqueda -->
        <input type="text" placeholder="Buscar...">
       
        </div>
        <br><br>

        <table id="tabla_usuarios">
            <tr>
                <th>Estado</th>
                <th>Nombre de Usuario</th>
                <th>Correo Electrónico</th>
                <th>Departamentos</th>
                <th>Acciones</th> <!-- Nueva columna para los botones de modificar -->
            </tr>
            <?php
            // Consultar la base de datos para obtener los usuarios autorizados y no autorizados
            $sql_usuarios_autorizados = "SELECT u.ID_USER, u.USER_NAME, u.email, u.estado, GROUP_CONCAT(g.NOMBRE_GRUPO) AS departamentos 
                            FROM usuario u
                            LEFT JOIN usuarioxgrupo ug ON u.ID_USER = ug.ID_USER
                            LEFT JOIN grupos g ON ug.ID_GRUPO = g.ID_GRUPO
                            WHERE u.estado IN ('autorizado', 'no_autorizado')
                            GROUP BY u.ID_USER";
            $result_usuarios_autorizados = $conn->query($sql_usuarios_autorizados);

            if ($result_usuarios_autorizados->num_rows > 0) {
                // Iterar sobre los resultados y mostrar cada usuario en la tabla
                while ($row = $result_usuarios_autorizados->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . ucfirst($row["estado"]) . "</td>"; // Columna "Estado"
                    echo "<td>" . $row["USER_NAME"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["departamentos"] . "</td>";
                    // Botón para modificar cada usuario
                    echo "<td><a href='modificar_user.php?id=" . $row["ID_USER"] . "'>Modificar</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay usuarios autorizados o no autorizados</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<div class="box2">
    <div class="box_form2">
    <h2 class="h3">Usuarios En Espera</h2>
    <input type="text" id="busqueda" placeholder="Buscar...">
    <table id="tabla_usuarios_espera">
        <tr>
            <th>Estado</th>
            <th>Nombre de Usuario</th>
            <th>Correo Electrónico</th>
            <th>Departamentos</th>
            <th>Acciones</th> <!-- Nueva columna para los botones de aceptar y rechazar -->
        </tr>
        <?php
        // Consultar la base de datos para obtener los usuarios en espera
        $sql_usuarios_espera = "SELECT u.ID_USER, u.USER_NAME, u.email, u.estado, GROUP_CONCAT(g.NOMBRE_GRUPO) AS departamentos 
                        FROM usuario u
                        LEFT JOIN usuarioxgrupo ug ON u.ID_USER = ug.ID_USER
                        LEFT JOIN grupos g ON ug.ID_GRUPO = g.ID_GRUPO
                        WHERE u.estado = 'espera'
                        GROUP BY u.ID_USER";
        $result_usuarios_espera = $conn->query($sql_usuarios_espera);

        if ($result_usuarios_espera->num_rows > 0) {
            // Iterar sobre los resultados y mostrar cada usuario en la tabla
            while ($row = $result_usuarios_espera->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . ucfirst($row["estado"]) . "</td>"; // Columna "Estado"
                echo "<td>" . $row["USER_NAME"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["departamentos"] . "</td>";
                // Botones para aceptar y rechazar cada usuario
                echo "<td>";
                echo "<form method='post' action='aceptar_usuario.php'>";
                echo "<input type='hidden' name='id_usuario' value='" . $row["ID_USER"] . "'>";
                echo "<input type='submit' name='aceptar' value='Aceptar'>";
                echo "</form>";
                echo "<form method='post' action='rechazar_usuario.php'>";
                echo "<input type='hidden' name='id_usuario' value='" . $row["ID_USER"] . "'>";
                echo "<input type='submit' name='rechazar' value='Rechazar'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No hay usuarios en espera</td></tr>";
        }
        ?>
    </table>
</div>
</div>

</body>
</html>
