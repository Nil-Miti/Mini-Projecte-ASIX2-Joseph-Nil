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

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
    <link rel="stylesheet" href="../css/panel_admin.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/dt/dt-2.0.7/datatables.min.css" rel="stylesheet">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-2.0.7/datatables.min.js"></script>
    <script defer src="script.js"></script>
    
    
</head>
	<body data-spy="scroll" data-target="#myScrollspy" data-offset="0">
	<div class="container-fluid" style="background-color:#2196F3;color:#fff;height:220px;">
	<!--
	  <img src="../css/logo.png" class="logo">
	  <div class="botones">
	    <button class="compartir"><a href="../compartir_archivo.php"><strong>Compartir</strong></a></button>
	    <button class="mis-archivos"><a href="../mis_archivos.php"><strong>Mis archivos</strong></a></button>
	    <button class="grupo"><a href="../grupos.php"><strong>Grupo</strong></a></button>
	    <button class="nombre"><strong><?php echo $_SESSION['nom']?></strong></button>
	  </div>
	 -->
	  </div>
	<br>


	<div class="container">
	<div class="row">
	    <nav class="col-sm-1" id="myScrollspy">
	      <ul class="nav nav-pills nav-stacked" data-spy="affix" data-offset-top="500">
		<li><a href="#zona1">Section 1</a></li>
		<li><a href="#zona2">Section 2</a></li>
		<li><a href="#zona3">Section 3</a></li> 
	     </ul>
	   </nav>
	   
	<div class="col-sm-10">
	<div id="zona1">
	<table id="section1" class="table table-striped" style="width:100%">
		<thead>
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
		</tfoot>
	    </table>
	 </div>
	 <br>
	 <div id="zona2">
	 <table id="section2" class="table table-striped" style="width:100%">
		<thead>
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
		    </tfoot>
		</table>
	</div>
	<br>
	<div id="zona3">
	 <table id="section3" class="table table-striped" style="width:100%">
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
		    </tfoot>
		</table>
	</div>
	 </div>
	 </div>
	 </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	</body>
</html>
