<?php
	session_start();
	require '../vendor/autoload.php';

	// Verificar la autenticación del usuario y los permisos
	if (!(isset($_SESSION['id_usuario']) && ($_SESSION['nom'] == 'joseph' || $_SESSION['nom'] == 'nil'))) {
	    header("Location: /auth/login.php");
	    exit();
	}

	$servername = "localhost";
	$username = "admin";
	$password = "1234";
	$dbname = "server";

	$conn = new mysqli($servername, $username, $password, $dbname);

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
    <link rel="stylesheet" href="../css/panel_admin.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"   crossorigin="anonymous">

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

<h1>Panel de Administrador</h1>
<div class="box1">
    <div class="box_form">
	<h2 class="h2">Usuarios Registrados</h2>
	<div class="acciones">
	    <input type="text" id="buscar_registrados" placeholder="Buscar..."> <!-- Placeholder para búsqueda -->
	</div>
	<br><br>

	<table class="table table-dark table-striped">
	    <tr>
	        <th>Estado</th>
	        <th>Nombre de Usuario</th>
	        <th>Correo Electrónico</th>
	        <th>Departamentos</th>
	        <th>Acciones</th>
	    </tr>
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
	</table>
    </div>
</div>


<div class="box2">
    <div class="box_form2">
	<h2 class="h3">Usuarios En Espera</h2>
	<input type="text" id="buscar_espera" placeholder="Buscar..."> <!-- Placeholder para búsqueda -->
	<table id="tabla_usuarios_espera">
	    <tr>
	        <th>Estado</th>
	        <th>Nombre de Usuario</th>
	        <th>Correo Electrónico</th>
	        <th class="btt_acciones">Acciones</th>
	    </tr>
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
	            echo "<form method='post' action='aceptar_usuario.php'>";
	            echo "<input type='hidden' name='id_usuario' value='" . $row["ID_USER"] . "'>";
	            echo "<input class='aceptar' type='submit' name='aceptar' value='Aceptar'>";
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

<div class="mongodb">
<div class="acciones-mongo">
<h1 class="h1mongodb">Historial de ficheros</h1>
</div>
<table class="mongodb" id="tabla_mongodb">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cliente = new MongoDB\Client("mongodb://localhost:27017");
        $base_de_datos = $cliente->registros;
        $coleccion = $base_de_datos->archivos;

        $resultados = $coleccion->find([]);

        foreach ($resultados as $documento) {
            echo "<tr>";
            echo "<td>" . $documento['nombre'] . "</td>";
            echo "<td>" . $documento['ubicacion'] . "</td>";
            echo "<td>" . $documento['estado'] . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
	</table>
	<div class="pagination" id="pagination_mongodb"></div>
</div>
	
<div class="crear_grupo">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "admin";
    $password = "1234";
    $dbname = "server";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $nombre = $_POST['nombre'];

    $sql_check_grupo = "SELECT * FROM grupos WHERE NOMBRE_GRUPO = ?";
    $stmt_check_grupo = $conn->prepare($sql_check_grupo);
    $stmt_check_grupo->bind_param("s", $nombre);
    $stmt_check_grupo->execute();
    $result_check_grupo = $stmt_check_grupo->get_result();

    if ($result_check_grupo->num_rows > 0) {
        echo "El grupo ya existe";
    } else {
        $sql_insert_grupo = "INSERT INTO grupos (NOMBRE_GRUPO) VALUES (?)";
        $stmt_insert_grupo = $conn->prepare($sql_insert_grupo);
        $stmt_insert_grupo->bind_param("s", $nombre);

        if ($stmt_insert_grupo->execute()) {
            echo "Grupo creado con éxito";

            $id_grupo = $conn->insert_id;
            $usuario = $_SESSION['nom'];
            $ruta_carpeta = "/var/www/servidor/grupos/" . $nombre;
            if (!file_exists($ruta_carpeta)) {
                mkdir($ruta_carpeta);
            }
        } else {
            echo "Error al crear el grupo";
        }

        $stmt_insert_grupo->close();
    }

    $stmt_check_grupo->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/panel_admin.css"/>
    <title>Crear Grupo</title>
</head>
<body>

<form class="form4" action="panel_admin.php" method="post">
    <label for="nombre">Crear nuevo grupo:</label>
    <input type="text" id="nombre" name="nombre" required><br>
    <input type="submit" value="Crear grupo">
</form>
</body>
</html>
</div>

<script>
    document.getElementById("buscar_registrados").addEventListener("input", function() {
	var filtro = this.value.toUpperCase();
	var tabla = document.getElementById("tabla_usuarios_registrados");
	var filas = tabla.getElementsByTagName("tr");
	for (var i = 0; i < filas.length; i++) {
	    var celda = filas[i].getElementsByTagName("td")[1];
	    if (celda) {
	        var textoCelda = celda.textContent || celda.innerText;
	        if (textoCelda.toUpperCase().indexOf(filtro) > -1) {
	            filas[i].style.display = "";
	        } else {
	            filas[i].style.display = "none";
	        }
	    }
	}
    });

    document.getElementById("buscar_espera").addEventListener("input", function() {
	var filtro = this.value.toUpperCase();
	var tabla = document.getElementById("tabla_usuarios_espera");
	var filas = tabla.getElementsByTagName("tr");
	for (var i = 0; i < filas.length; i++) {
	    var celda = filas[i].getElementsByTagName("td")[1];
	    if (celda) {
	        var textoCelda = celda.textContent || celda.innerText;
	        if (textoCelda.toUpperCase().indexOf(filtro) > -1) {
	            filas[i].style.display = "";
	        } else {
	            filas[i].style.display = "none";
	        }
	    }
	}
    });
    
    window.onload = function () {
        var table = document.getElementById('tabla_mongodb');
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        var rowsPerPage = 4;
        var totalPages = Math.ceil(rows.length / rowsPerPage);
        var currentPage = 1;

        function showPage(page) {
            currentPage = page;
            for (var i = 0; i < rows.length; i++) {
                if (i < page * rowsPerPage && i >= (page - 1) * rowsPerPage) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
            renderPagination();
        }

        function renderPagination() {
            var pagination = document.getElementById('pagination_mongodb');
            pagination.innerHTML = '';

            for (var i = 1; i <= totalPages; i++) {
                var link = document.createElement('a');
                link.href = '#';
                link.textContent = i;
                if (i === currentPage) {
                    link.classList.add('active');
                }
                link.onclick = function () {
                    showPage(parseInt(this.textContent));
                    return false;
                };
                pagination.appendChild(link);
            }
        }

        showPage(currentPage);
    };
</script>
</body>
</html>
