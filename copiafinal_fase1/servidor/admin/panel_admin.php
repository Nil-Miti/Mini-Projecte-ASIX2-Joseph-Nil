<?php
require '../vendor/autoload.php';

// Verificar la autenticación del usuario y los permisos

$servername = "localhost";
$username = "admin";
$password = "1234";
$dbname = "server";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Conexión a MongoDB
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$database = $mongoClient->registros;
$collection = $database->archivos;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crear_grupo'])) {
        $nombre_grupo = $_POST['nombre_grupo'];
        $sql_insert_grupo = "INSERT INTO grupos (NOMBRE_GRUPO) VALUES ('$nombre_grupo')";
        if ($conn->query($sql_insert_grupo) === TRUE) {
            echo "Nuevo grupo creado correctamente.";
        } else {
            echo "Error: " . $sql_insert_grupo . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['eliminar_archivo'])) {
        $archivo_id = $_POST['archivo_id'];
        $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($archivo_id)]);
        echo "Archivo eliminado correctamente.";
    } elseif (isset($_POST['aceptar_usuario'])) {
        $id_usuario = $_POST['id_usuario'];
        $sql_aceptar_usuario = "UPDATE usuario SET estado = 'autorizado' WHERE ID_USER = $id_usuario";
        if ($conn->query($sql_aceptar_usuario) === TRUE) {
            echo "Usuario aceptado correctamente.";
        } else {
            echo "Error: " . $sql_aceptar_usuario . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['rechazar_usuario'])) {
        $id_usuario = $_POST['id_usuario'];
        $sql_rechazar_usuario = "UPDATE usuario SET estado = 'no_autorizado' WHERE ID_USER = $id_usuario";
        if ($conn->query($sql_rechazar_usuario) === TRUE) {
            echo "Usuario rechazado correctamente.";
        } else {
            echo "Error: " . $sql_rechazar_usuario . "<br>" . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js"></script>
    <style>
        body {
            position: relative;
        }
        .affix {
            top: 20px;
            z-index: 9999 !important;
        }
        .container-fluid {
            background-color:#2196F3;
            color:#fff;
            height:220px;
        }
        .nav-pills>li.active>a {
            color: #fff;
            background-color: #337ab7;
        }
        .section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body data-spy="scroll" data-target="#myScrollspy" data-offset="20">

<div class="container-fluid">
    <h1>Panel de Administración</h1>
    <p>Bienvenido, <?php echo $_SESSION['nom']; ?></p>
</div>
<br>

<div class="container">
    <div class="row">
        <nav class="col-sm-3" id="myScrollspy">
            <ul class="nav nav-pills nav-stacked" data-spy="affix" data-offset-top="205">
                <li class="active"><a href="#usuarios">Usuarios</a></li>
                <li><a href="#usuarios_espera">Usuarios en Espera</a></li>
                <li><a href="#archivos">Archivos</a></li>
                <li><a href="#creacion_grupo">Creación de Grupo</a></li>
            </ul>
        </nav>
        <div class="col-sm-9">
            <div id="usuarios" class="section">
                <h2>Usuarios</h2>
                <table id="section1" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Nombre de Usuario</th>
                            <th>Correo Electrónico</th>
                            <th>Departamentos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_usuarios_autorizados = "SELECT u.ID_USER, u.USER_NAME, u.email, u.estado, GROUP_CONCAT(g.NOMBRE_GRUPO) AS departamentos
                                            FROM usuario u
                                            LEFT JOIN usuarioxgrupo ug ON u.ID_USER = ug.ID_USER
                                            LEFT JOIN grupos g ON ug.ID_GRUPO = g.ID_GRUPO
                                            WHERE u.estado IN ('autorizado', 'no_autorizado')
                                            GROUP BY u.ID_USER";
                        $result_usuarios_autorizados = $conn->query($sql_usuarios_autorizados);

                        if ($result_usuarios_autorizados->num_rows > 0) {
                            while ($row = $result_usuarios_autorizados->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . ucfirst($row["estado"]) . "</td>";
                                echo "<td>" . $row["USER_NAME"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>" . $row["departamentos"] . "</td>";
                                echo "<td><a href='modificar_user.php?id=" . $row["ID_USER"] . "'>Modificar</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hay usuarios autorizados o no autorizados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="usuarios_espera" class="section">
                <h2>Usuarios en Espera</h2>
                <table id="section2" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Nombre de Usuario</th>
                            <th>Correo Electrónico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_usuarios_espera = "SELECT u.ID_USER, u.USER_NAME, u.email, u.estado, GROUP_CONCAT(g.NOMBRE_GRUPO) AS departamentos
                                            FROM usuario u
                                            LEFT JOIN usuarioxgrupo ug ON u.ID_USER = ug.ID_USER
                                            LEFT JOIN grupos g ON ug.ID_GRUPO = g.ID_GRUPO
                                            WHERE u.estado = 'espera'
                                            GROUP BY u.ID_USER";
                        $result_usuarios_espera = $conn->query($sql_usuarios_espera);

                        if ($result_usuarios_espera->num_rows > 0) {
                            while ($row = $result_usuarios_espera->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . ucfirst($row["estado"]) . "</td>";
                                echo "<td>" . $row["USER_NAME"] . "</td>";
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>";
                                echo "<form method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='id_usuario' value='" . $row["ID_USER"] . "'>";
                                echo "<input class='btn btn-success' type='submit' name='aceptar_usuario' value='Aceptar'>";
                                echo "</form>";
                                echo "<form method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='id_usuario' value='" . $row["ID_USER"] . "'>";
                                echo "<input class='btn btn-danger' type='submit' name='rechazar_usuario' value='Rechazar'>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No hay usuarios en espera</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="archivos" class="section">
                <h2>Archivos</h2>
                <table id="section3" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $archivos = $collection->find();

                        foreach ($archivos as $archivo) {
                            echo "<tr>";
                            echo "<td>" . $archivo['nombre'] . "</td>";
                            echo "<td>" . $archivo['ubicacion'] . "</td>";
                            echo "<td>" . $archivo['estado'] . "</td>";
                            echo "<td>" . $archivo['fecha'] . "</td>";
                            echo "<td>";
                            echo "<form method='post' style='display:inline;'>";
                            echo "<input type='hidden' name='archivo_id' value='" . $archivo['_id'] . "'>";
                            echo "<input class='btn btn-danger' type='submit' name='eliminar_archivo' value='Eliminar'>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="creacion_grupo" class="section">
                <h2>Creación de Grupo</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="nombre_grupo">Nombre del Grupo:</label>
                        <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="crear_grupo">Crear Grupo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#section1').DataTable();
    $('#section2').DataTable();
    $('#section3').DataTable();
});
</script>

</body>
</html>
