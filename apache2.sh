#!/bin/bash

sudo apt install apache2
sudo apt update
sudo apt install php
sudo ufw allow 'Apache'
sudo systemctl start apache2
sudo snap install curl
curl -4 icanhazip.com
cd /var/www
sudo mkdir servidor
sudo chown -R servidor:servidor servidor/
sudo chmod -R 755 servidor/
cd servidor/
sudo touch index.html
sudo chmod 777 index.html
sudo echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Archivo</title>
</head>
<body>
    <h1>Subir Archivo</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Seleccionar Archivo:</label>
        <input type="file" name="file" id="file" required>
        <br>
        <input type="submit" value="Subir Archivo">
    </form>
</body>
</html>' > index.html

cd /etc/apache2/sites-avaiable/
sudo touch /etc/apache/sites-avaiable/servidor.conf
sudo chmod 777 servidor.conf
sudo echo '<VirtualHost *:80>      
    ServerAdmin webmaster@localhost
    ServerName servidor
    ServerAlias www.servidor
    DocumentRoot /var/www/servidor
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>' > servidor.conf
sudo a2ensite servidor.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
cd /var/www/servidor
sudo mkdir css
cd css/
touch styles.css
sudo chmod 777 styles.css
echo 'body {
    font-family: Arial, sans-serif;
    margin: 20px;
    padding: 20px;
}

h1 {
    color: #333;
}

form {
    margin-top: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="file"] {
    margin-bottom: 10px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}' > styles.css
sudo systemctl restart apache2
cd /var/www/servidor
sudo mkdir auth
cd auth/
sudo touch login.php
sudo chmod 777 login.php
sudo echo '<?php
session_start();

if (isset($_REQUEST['login'])) {
    //CONEXION A LA BASE DE DATOS
    $servername ="";
    $database = ""; 
    $username = "";
    $password = "";
    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!conn) die("Conexión erronea: " . mysqli_connect_error());
// Utilizar consultas parametrizadas
    $sql = "SELECT * FROM users WHERE user = ? AND pass = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $_REQUEST['user'], $_REQUEST['pass']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    } 
else {
        $_SESSION['valido'] = 0;
        echo "<h1>Usuario erróneo o contraseña erróneos</h1>";
    }

    // Cerrar la conexión
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>
<body>
            <form action="login" method="post">
                <h1>Iniciar sesión</h1>
                <p>
                    <input type="text" placeholder="Nombre de usuario" name="user" />
                    <input type="password" placeholder="Contraseña" name="pass" />
                    <button type="submit" name="login">Iniciar sesión</button>
                </p>
            </form>
</body>
</html>' > login.php
cd /var/www/servidor/css
sudo touch login.css
sudo chmod 777 login.css
sudo echo 'body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

h1 {
    color: #333;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    box-sizing: border-box;
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}' > login.css
cd /var/www/servidor/auth
sudo touch crear_user.php
sudo chmod 777 crear_user.php
sudo echo '<?php
session_start();
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>
<body>
    <?php
    if ((isset($_SESSION['valido'])) && ($_SESSION['valido'] == 1)) {
        if (isset($_REQUEST['alta'])) {
            // Conexión a la BD
            $servername = "";
            $database = "";
            $username = "";
            $password = "";
            $conn = mysqli_connect($servername, $username, $password, $database>
            if (!$conn) {
                die("Connexió errònea: " . mysqli_connect_error());}

            $id = $_REQUEST['id'];
            // Verificar si el ID de usuario ya está en uso
            $sql = "SELECT * FROM users WHERE id = '".$id."'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                ?>
                <h1><?php echo "El ID de usuario ya esta en uso. Canvie el ID p>
// Alta de registro
                $sql = "INSERT INTO users (user, id, pass, user_type) values 
                        ('".$_REQUEST['user']."',
                        '".$id."',
                        '".$_REQUEST['pass']."',
                        '".$_REQUEST['user_type']."')";

                if (mysqli_query($conn, $sql)) {
                    ?>
<h1><?php echo "Usuario creado con éxito"; ?></h1>

                   <?php
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    ?>
<?php
                }
            }

            mysqli_close($conn);
        } else {
        ?>
            <form action="alta" method="post">
                Usuario: <input type="text" name="user" placeholder="Nombre de >
                ID: <input type="text" name="id" placeholder="ID del usuario"><>
                Password: <input type="password" name="pass" placeholder="Contr>
                <input type="submit" value="Crear" name="crear">     
            </form>
        <?php 
        } 
    } else {
        ?>
                <h1><?php echo "No te has validado! Clic en 'Volver al inicio' >
                <?php
    }
    ?>
</body>
</html>' > crear_user.php
cd /var/www/servidor
sudo touch upload.php
sudo chmod 777 upload.php
sudo echo '<?php
$target_dir = "uploads/";  // Carpeta donde se guardarán los archivos
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Verificar si el archivo es una imagen real o falso
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["file"]["tmp_name"]);
    if ($check !== false) {
        echo "El archivo es una imagen - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }
}

// Verificar si el archivo ya existe
if (file_exists($target_file)) {
    echo "Lo siento, el archivo ya existe.";
    $uploadOk = 0;
}

// Verificar el tamaño del archivo
if ($_FILES["file"]["size"] > 500000) {
    echo "Lo siento, el archivo es demasiado grande.";
    $uploadOk = 0;
}

// Permitir solo ciertos formatos de archivo
$allowed_formats = array("jpg", "jpeg", "png", "gif");
if (!in_array($imageFileType, $allowed_formats)) {
    echo "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
    $uploadOk = 0;
}

// Verificar si $uploadOk es 0 por un error
if ($uploadOk == 0) {
    echo "Lo siento, tu archivo no fue cargado.";
} else {
    // Si todo está bien, intenta cargar el archivo
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "El archivo " . basename($_FILES["file"]["name"]) . " ha sido cargado.";
    } else {
        echo "Lo siento, hubo un error al cargar tu archivo.";   
        }
}
?>' > upload.php
cd /var/www/servidor
mkdir uploads
chmod 777 uploads
sudo systemctl restart apache2
chmod +x "$0"




