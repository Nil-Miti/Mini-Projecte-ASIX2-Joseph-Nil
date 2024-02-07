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

    $sql = "SELECT * FROM usuario WHERE USER_NAME = ? AND PASSW = ? AND email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Corrige el error aquí: cambia el punto por una coma
    mysqli_stmt_bind_param($stmt, "ss", $_REQUEST['USER_NAME'], $_REQUEST['PASSW'], $_REQUEST['email']);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Página</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>

    <div id="container">
        <div id="logo-container">
            <img src="../css/logo.png" alt="Logo" id="logo">
        </div>

        <div id="text-container">
            <h1 id="titulo-principal">INICIA ARA</h1>
            <div id="button-container">
                <button class="button" id="create-account-btn" onclick="openCreateAccountModal()">Crea un compte</button>
                <p id="subtitle">¿Ya tienes una cuenta?</p>
                <button class="button" id="login-btn" onclick="AbrirCajaIniciarSesion()">Inicia la sessió</button>
            </div>
        </div>
    </div>

         <!-- Caja de Crear Cuenta -->
            <div id="create-account-modal">
                <div id="modal-content">
                    <div id="close-btn" onclick="closeCreateAccountModal()"> <!-- Agregado el contenedor para la "x" -->
                    <div class="close-icon">&#10006;</div>
                    </div>  
                    <h2>Crea tu cuenta</h2>
                        <div class="input-container">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" placeholder="Ingrese su usuario">
                    </div>
                    <div class="input-container">
                        <label for="email">Correo</label>
                        <input type="email" id="email" placeholder="Ingrese su correo">
                    </div>
                    <div class="input-container">
<label for="password">Contraseña</label>
                        <input type="password" id="password" placeholder="Ingrese su contraseña">
                    </div>
                    <button id="create-account-button" onclick="createAccount()">Crear un compte</button>
                </div>
            </div>


        <!-- Caja de Inciar sessio -->
        <form action="login.php" method="post">
        <div id="login-modal">
                <div id="caja-contenido">
                    <div id="close-btn" onclick="closeCreateAccountModal()"> <!-- Agregado el contenedor para la "x" -->
                    <div class="close-icon">&#10006;</div>
                    </div>
                    <h2>Inicia Sessio</h2>
                        <div class="input-container">
                        <label for="username">Usuario</label>
                        <input type="text" id="USER_NAME" placeholder="Ingrese su user o su correo">
                    </div>
                    <div class="input-container">
                        <label for="password">Contraseña</label>
                        <input type="password" id="PASSW" placeholder="Ingrese su contraseña">
                    </div>
                    <button type="submit" name= "login" >Crear un compte</button>
        </form>
                </div>
            </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const createAccountBtn = document.getElementById("create-account-btn");
        const loginBtn = document.getElementById("login-btn");
const createAccountModal = document.getElementById("create-account-modal");
        const loginModal = document.getElementById("login-modal"); // Cambio aquí
        const closeModalBtn = document.getElementById("close-btn");

        createAccountBtn.addEventListener("click", function () {
                createAccountModal.classList.add("show-modal");
        });

        loginBtn.addEventListener("click", function () {
                loginModal.classList.add("show-modal"); // Cambio aquí
        });

        closeModalBtn.addEventListener("click", function () {
                createAccountModal.classList.remove("show-modal");
                loginModal.classList.remove("show-modal"); // Cambio aquí
        });
    });
    </script>

<script src="index.js"></script>

</body>
</html>
